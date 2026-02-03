<?php

namespace App\Services\Forum;

use App\Models\User;
use App\Services\Forum\ForumRoleAccessService;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;

class ForumSearchService
{
    protected $roleAccessService;

    public function __construct(ForumRoleAccessService $roleAccessService)
    {
        $this->roleAccessService = $roleAccessService;
    }

    /**
     * Perform advanced search across threads and posts
     * 
     * @param User $user
     * @param array $params
     * @return array
     */
    public function search(User $user, array $params): array
    {
        $query = $params['query'] ?? '';
        $author = $params['author'] ?? null;
        $categoryId = $params['category_id'] ?? null;
        $dateFrom = $params['date_from'] ?? null;
        $dateTo = $params['date_to'] ?? null;
        $status = $params['status'] ?? null; // 'open', 'closed', 'all'
        $hasAnswers = $params['has_answers'] ?? null; // true, false, null
        $sortBy = $params['sort_by'] ?? 'relevance'; // 'relevance', 'date', 'popularity'
        $searchIn = $params['search_in'] ?? 'all'; // 'all', 'threads', 'posts'

        // Build thread query
        $threadQuery = Thread::query()
            ->with(['category', 'author', 'firstPost'])
            ->whereNull('deleted_at');

        // Build post query
        $postQuery = Post::query()
            ->with(['thread.category', 'author', 'thread'])
            ->whereNull('deleted_at'); // Posts have soft deletes

        // Apply role-based filtering
        $accessibleCategoryIds = $this->getAccessibleCategoryIds($user);
        $threadQuery->whereIn('category_id', $accessibleCategoryIds);
        
        // For posts, filter by accessible categories through threads
        $postQuery->whereHas('thread', function ($q) use ($accessibleCategoryIds) {
            $q->whereIn('category_id', $accessibleCategoryIds);
        });

        // Apply text search
        if (!empty($query)) {
            $searchTerms = $this->parseSearchQuery($query);
            
            if ($searchIn === 'all' || $searchIn === 'threads') {
                $threadQuery->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $q->where(function ($subQ) use ($term) {
                            $subQ->where('title', 'LIKE', "%{$term}%")
                                 ->orWhereHas('firstPost', function ($postQ) use ($term) {
                                     $postQ->where('content', 'LIKE', "%{$term}%");
                                 });
                        });
                    }
                });
            }

            if ($searchIn === 'all' || $searchIn === 'posts') {
                $postQuery->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $q->where('content', 'LIKE', "%{$term}%");
                    }
                });
            }
        }

        // Filter by author
        if ($author) {
            $authorIds = User::where(function ($q) use ($author) {
                $q->where('first_name', 'LIKE', "%{$author}%")
                  ->orWhere('last_name', 'LIKE', "%{$author}%")
                  ->orWhere('email', 'LIKE', "%{$author}%");
            })->pluck('id');

            if ($searchIn === 'all' || $searchIn === 'threads') {
                $threadQuery->whereIn('author_id', $authorIds);
            }
            if ($searchIn === 'all' || $searchIn === 'posts') {
                $postQuery->whereIn('author_id', $authorIds);
            }
        }

        // Filter by category
        if ($categoryId) {
            if ($searchIn === 'all' || $searchIn === 'threads') {
                $threadQuery->where('category_id', $categoryId);
            }
            if ($searchIn === 'all' || $searchIn === 'posts') {
                $postQuery->whereHas('thread', function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                });
            }
        }

        // Filter by date range
        if ($dateFrom) {
            if ($searchIn === 'all' || $searchIn === 'threads') {
                $threadQuery->where('created_at', '>=', $dateFrom);
            }
            if ($searchIn === 'all' || $searchIn === 'posts') {
                $postQuery->where('created_at', '>=', $dateFrom);
            }
        }

        if ($dateTo) {
            if ($searchIn === 'all' || $searchIn === 'threads') {
                $threadQuery->where('created_at', '<=', $dateTo);
            }
            if ($searchIn === 'all' || $searchIn === 'posts') {
                $postQuery->where('created_at', '<=', $dateTo);
            }
        }

        // Filter by thread status
        if ($status === 'open') {
            if ($searchIn === 'all' || $searchIn === 'threads') {
                $threadQuery->where('locked', false);
            }
            if ($searchIn === 'all' || $searchIn === 'posts') {
                $postQuery->whereHas('thread', function ($q) {
                    $q->where('locked', false);
                });
            }
        } elseif ($status === 'closed') {
            if ($searchIn === 'all' || $searchIn === 'threads') {
                $threadQuery->where('locked', true);
            }
            if ($searchIn === 'all' || $searchIn === 'posts') {
                $postQuery->whereHas('thread', function ($q) {
                    $q->where('locked', true);
                });
            }
        }

        // Filter by has answers (for question threads)
        if ($hasAnswers !== null) {
            if ($hasAnswers) {
                if ($searchIn === 'all' || $searchIn === 'threads') {
                    $threadQuery->whereNotNull('best_answer_post_id');
                }
                if ($searchIn === 'all' || $searchIn === 'posts') {
                    $postQuery->whereHas('thread', function ($q) {
                        $q->whereNotNull('best_answer_post_id');
                    });
                }
            } else {
                if ($searchIn === 'all' || $searchIn === 'threads') {
                    $threadQuery->where(function ($q) {
                        $q->whereNull('best_answer_post_id')
                          ->orWhere('is_question', false);
                    });
                }
                if ($searchIn === 'all' || $searchIn === 'posts') {
                    $postQuery->whereHas('thread', function ($q) {
                        $q->where(function ($subQ) {
                            $subQ->whereNull('best_answer_post_id')
                                 ->orWhere('is_question', false);
                        });
                    });
                }
            }
        }

        // Get results
        $threads = collect();
        $posts = collect();

        if ($searchIn === 'all' || $searchIn === 'threads') {
            // Apply sorting
            if ($sortBy === 'date') {
                $threadQuery->orderBy('created_at', 'desc');
            } elseif ($sortBy === 'popularity') {
                $threadQuery->orderBy('reply_count', 'desc')
                           ->orderBy('created_at', 'desc');
            } else {
                // Relevance: prioritize matches in title, then by reply count
                $threadQuery->orderByRaw("CASE WHEN title LIKE ? THEN 1 ELSE 2 END", ["%{$query}%"])
                           ->orderBy('reply_count', 'desc')
                           ->orderBy('created_at', 'desc');
            }

            $threads = $threadQuery->get();
        }

        if ($searchIn === 'all' || $searchIn === 'posts') {
            // Apply sorting
            if ($sortBy === 'date') {
                $postQuery->orderBy('created_at', 'desc');
            } elseif ($sortBy === 'popularity') {
                $postQuery->orderBy('created_at', 'desc');
            } else {
                // Relevance: prioritize matches in content
                $postQuery->orderBy('created_at', 'desc');
            }

            $posts = $postQuery->get();
        }

        // Highlight search terms in results
        $threads = $threads->map(function ($thread) use ($query) {
            $thread->highlighted_title = $this->highlightText($thread->title, $query);
            if ($thread->firstPost) {
                $thread->highlighted_content = $this->highlightText(
                    $this->truncateContent($thread->firstPost->content, 200),
                    $query
                );
            }
            return $thread;
        });

        $posts = $posts->map(function ($post) use ($query) {
            $post->highlighted_content = $this->highlightText(
                $this->truncateContent($post->content, 200),
                $query
            );
            return $post;
        });

        return [
            'threads' => $threads,
            'posts' => $posts,
            'total_results' => $threads->count() + $posts->count(),
            'query' => $query,
        ];
    }

    /**
     * Get accessible category IDs for user based on role restrictions
     * 
     * @param User $user
     * @return array
     */
    protected function getAccessibleCategoryIds(User $user): array
    {
        // forum_categories table doesn't have deleted_at column, so no need to filter
        $allCategories = Category::all();
        $accessibleIds = [];

        foreach ($allCategories as $category) {
            if ($this->roleAccessService->canAccessCategory($user, $category->id)) {
                $accessibleIds[] = $category->id;
            }
        }

        return $accessibleIds;
    }

    /**
     * Parse search query into terms
     * 
     * @param string $query
     * @return array
     */
    protected function parseSearchQuery(string $query): array
    {
        // Remove special characters and split by spaces
        $query = trim($query);
        $terms = preg_split('/\s+/', $query);
        
        // Filter out empty terms and limit to 10 terms
        $terms = array_filter($terms, function ($term) {
            return strlen($term) >= 2; // Minimum 2 characters
        });

        return array_slice($terms, 0, 10);
    }

    /**
     * Highlight search terms in text
     * 
     * @param string $text
     * @param string $query
     * @return string
     */
    protected function highlightText(string $text, string $query): string
    {
        if (empty($query)) {
            return $text;
        }

        $terms = $this->parseSearchQuery($query);
        $highlighted = $text;

        foreach ($terms as $term) {
            $pattern = '/\b(' . preg_quote($term, '/') . ')\b/i';
            $highlighted = preg_replace(
                $pattern,
                '<mark class="bg-yellow-200 font-semibold">$1</mark>',
                $highlighted
            );
        }

        return $highlighted;
    }

    /**
     * Truncate content to specified length
     * 
     * @param string $content
     * @param int $length
     * @return string
     */
    protected function truncateContent(string $content, int $length = 200): string
    {
        $content = strip_tags($content);
        if (strlen($content) <= $length) {
            return $content;
        }

        return substr($content, 0, $length) . '...';
    }

    /**
     * Get similar threads based on search query
     * 
     * @param User $user
     * @param string $query
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getSimilarThreads(User $user, string $query, int $limit = 5)
    {
        if (empty($query)) {
            return collect();
        }

        $terms = $this->parseSearchQuery($query);
        $accessibleCategoryIds = $this->getAccessibleCategoryIds($user);

        $threadQuery = Thread::query()
            ->with(['category', 'author'])
            ->whereIn('category_id', $accessibleCategoryIds)
            ->whereNull('deleted_at')
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('title', 'LIKE', "%{$term}%");
                }
            })
            ->orderBy('reply_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        return $threadQuery->get();
    }
}
