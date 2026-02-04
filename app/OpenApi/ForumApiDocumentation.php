<?php

namespace App\OpenApi;

/**
 * @OA\Tag(
 *     name="Department Forum - Categories",
 *     description="Department Forum Category Management APIs"
 * )
 * 
 * @OA\Tag(
 *     name="Department Forum - Threads",
 *     description="Department Forum Thread Management APIs"
 * )
 * 
 * @OA\Tag(
 *     name="Department Forum - Posts",
 *     description="Department Forum Post Management APIs"
 * )
 * 
 * @OA\Tag(
 *     name="Department Forum - Bulk Actions",
 *     description="Department Forum Bulk Operations APIs"
 * )
 */

/**
 * @OA\Get(
 *     path="/forum/api/category",
 *     summary="Get all forum categories",
 *     description="Retrieve a hierarchical list of all forum categories that the authenticated user has access to. This endpoint returns the complete category tree structure, including nested subcategories. Categories are filtered based on user permissions - private categories are only shown to authorized users. Use the optional parent_id parameter to fetch only direct descendants of a specific category. The response includes category metadata such as thread count, post count, newest thread information, and available actions. This is typically the first API call when building a forum navigation menu or category listing page.",
 *     tags={"Department Forum - Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="parent_id",
 *         in="query",
 *         description="Filter categories by parent ID",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of categories",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="General Discussion"),
 *                 @OA\Property(property="description", type="string", example="General forum discussions"),
 *                 @OA\Property(property="accepts_threads", type="boolean", example=true),
 *                 @OA\Property(property="newest_thread_id", type="integer", nullable=true, example=5),
 *                 @OA\Property(property="latest_active_thread_id", type="integer", nullable=true, example=5),
 *                 @OA\Property(property="thread_count", type="integer", example=10),
 *                 @OA\Property(property="post_count", type="integer", example=50),
 *                 @OA\Property(property="is_private", type="boolean", example=false),
 *                 @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
 *                 @OA\Property(property="color_light_mode", type="string", nullable=true),
 *                 @OA\Property(property="color_dark_mode", type="string", nullable=true),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time"),
 *                 @OA\Property(property="actions", type="object",
 *                     @OA\Property(property="patch:update", type="string", example="/forum/api/category/1"),
 *                     @OA\Property(property="delete:delete", type="string", example="/forum/api/category/1")
 *                 )
 *             ))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Get(
 *     path="/forum/api/category/{category}",
 *     summary="Get a specific category",
 *     description="Retrieve complete details of a specific forum category by its ID. This endpoint provides comprehensive information about a single category including its title, description, thread/post counts, privacy settings, color schemes, and hierarchical relationships. The response includes links to related resources such as the newest thread and latest active thread. Access is controlled - if the category is private, only authorized users can view it. Use this endpoint when displaying category details, breadcrumbs, or when you need to verify category properties before performing operations on it.",
 *     tags={"Department Forum - Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="category",
 *         in="path",
 *         description="Category ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category details",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="General Discussion"),
 *                 @OA\Property(property="description", type="string", example="General forum discussions"),
 *                 @OA\Property(property="accepts_threads", type="boolean", example=true),
 *                 @OA\Property(property="thread_count", type="integer", example=10),
 *                 @OA\Property(property="post_count", type="integer", example=50),
 *                 @OA\Property(property="is_private", type="boolean", example=false)
 *             ),
 *             @OA\Property(property="links", type="object",
 *                 @OA\Property(property="self", type="string", example="/forum/api/category/1"),
 *                 @OA\Property(property="newest_thread", type="string", nullable=true),
 *                 @OA\Property(property="latest_active_thread", type="string", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Category not found"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/category",
 *     summary="Create a new category",
 *     description="Create a new forum category in the system. This endpoint requires administrator permissions. You can create both top-level categories and nested subcategories by specifying a parent_id. Categories can be configured as private (is_private=true) to restrict access, or as public. The accepts_threads flag determines whether users can create threads directly in this category or if it's only a container for subcategories. Color schemes for light and dark modes can be customized. The title field is required and must be at least 3 characters long. Upon successful creation, the new category is returned with its assigned ID and all metadata.",
 *     tags={"Department Forum - Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title"},
 *             @OA\Property(property="title", type="string", example="New Category"),
 *             @OA\Property(property="description", type="string", example="Category description"),
 *             @OA\Property(property="accepts_threads", type="boolean", example=true),
 *             @OA\Property(property="is_private", type="boolean", example=false),
 *             @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
 *             @OA\Property(property="color_light_mode", type="string", nullable=true),
 *             @OA\Property(property="color_dark_mode", type="string", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="New Category")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error"),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Admin access required")
 * )
 * 
 * @OA\Patch(
 *     path="/forum/api/category/{category}",
 *     summary="Update a category",
 *     description="Update properties of an existing forum category. This endpoint requires administrator permissions. You can modify the category title, description, privacy settings, thread acceptance flag, and color schemes. All fields are optional - only include the fields you want to update. Changing is_private will affect user access immediately. Modifying accepts_threads will determine if new threads can be created in this category. The category ID in the path identifies which category to update. Returns the updated category object with all current values.",
 *     tags={"Department Forum - Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="category",
 *         in="path",
 *         description="Category ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string", example="Updated Category Title"),
 *             @OA\Property(property="description", type="string", example="Updated description"),
 *             @OA\Property(property="accepts_threads", type="boolean", example=true),
 *             @OA\Property(property="is_private", type="boolean", example=false)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Updated Category Title")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error"),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Admin access required")
 * )
 * 
 * @OA\Delete(
 *     path="/forum/api/category/{category}",
 *     summary="Delete a category",
 *     description="Permanently delete a forum category from the system. This endpoint requires administrator permissions and should be used with caution. Deleting a category will also delete all associated threads, posts, and subcategories in a cascading manner. This action cannot be undone. Before deletion, ensure that you want to remove all content within the category. The category ID in the path specifies which category to delete. Returns a success confirmation upon successful deletion.",
 *     tags={"Department Forum - Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="category",
 *         in="path",
 *         description="Category ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Admin access required")
 * )
 * 
 * @OA\Get(
 *     path="/forum/api/category/{category}/thread",
 *     summary="Get threads in a category",
 *     description="Retrieve a paginated list of all threads within a specific category. This endpoint supports optional date-based filtering to narrow down results. You can filter threads by creation date (created_after, created_before) or last update date (updated_after, updated_before) using YYYY-MM-DD format. Threads are returned in chronological order by default. For private categories, only threads that the user has permission to view are included. The response includes thread metadata such as author information, pin/lock status, reply counts, and timestamps. Use this endpoint to display the thread listing page for a category.",
 *     tags={"Department Forum - Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="category",
 *         in="path",
 *         description="Category ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="created_after",
 *         in="query",
 *         description="Filter threads created after this date (YYYY-MM-DD)",
 *         required=false,
 *         @OA\Schema(type="string", format="date", example="2026-01-01")
 *     ),
 *     @OA\Parameter(
 *         name="created_before",
 *         in="query",
 *         description="Filter threads created before this date (YYYY-MM-DD)",
 *         required=false,
 *         @OA\Schema(type="string", format="date", example="2026-12-31")
 *     ),
 *     @OA\Parameter(
 *         name="updated_after",
 *         in="query",
 *         description="Filter threads updated after this date (YYYY-MM-DD)",
 *         required=false,
 *         @OA\Schema(type="string", format="date", example="2026-01-01")
 *     ),
 *     @OA\Parameter(
 *         name="updated_before",
 *         in="query",
 *         description="Filter threads updated before this date (YYYY-MM-DD)",
 *         required=false,
 *         @OA\Schema(type="string", format="date", example="2026-12-31")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of threads",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="category_id", type="integer", example=1),
 *                 @OA\Property(property="author_id", type="integer", example=1),
 *                 @OA\Property(property="author_name", type="string", example="John Doe"),
 *                 @OA\Property(property="title", type="string", example="Thread Title"),
 *                 @OA\Property(property="pinned", type="boolean", example=false),
 *                 @OA\Property(property="locked", type="boolean", example=false),
 *                 @OA\Property(property="reply_count", type="integer", example=5),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             ))
 *         )
 *     ),
 *     @OA\Response(response=404, description="Category not found"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/category/{category}/thread",
 *     summary="Create a new thread in a category",
 *     description="Create a new discussion thread in the specified category. Both title and content are required fields. The title must be at least 3 characters long, and the content must also be at least 3 characters. The authenticated user automatically becomes the thread author. The category must accept threads (accepts_threads=true) and the user must have permission to create threads in that category. Upon creation, the thread is immediately available and the first post (containing the content) is created automatically. Returns the complete thread object including the generated ID, timestamps, and all metadata.",
 *     tags={"Department Forum - Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="category",
 *         in="path",
 *         description="Category ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title", "content"},
 *             @OA\Property(property="title", type="string", example="New Thread Title"),
 *             @OA\Property(property="content", type="string", example="Thread content here...")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Thread created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="New Thread Title"),
 *                 @OA\Property(property="category_id", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 */

/**
 * @OA\Get(
 *     path="/forum/api/thread/recent",
 *     summary="Get recent threads",
 *     description="Retrieve a list of recently active threads across all forum categories that the user has access to. Threads are sorted by their last activity (most recently updated first). This endpoint is useful for displaying a 'Recent Activity' or 'Latest Discussions' section on the forum homepage. Only threads from accessible categories are included - private category threads are filtered based on user permissions. The response includes essential thread information like title, author, reply count, and timestamps. This provides a quick overview of forum activity without needing to navigate through individual categories.",
 *     tags={"Department Forum - Threads"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of recent threads",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Thread Title"),
 *                 @OA\Property(property="author_name", type="string", example="John Doe"),
 *                 @OA\Property(property="reply_count", type="integer", example=5),
 *                 @OA\Property(property="created_at", type="string", format="date-time")
 *             ))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Get(
 *     path="/forum/api/thread/unread",
 *     summary="Get unread threads",
 *     description="Retrieve a personalized list of threads that contain unread posts for the authenticated user. This endpoint tracks which threads have new activity since the user last read them. Threads are considered unread if they have posts created or updated after the user's last read timestamp. This is essential for building notification systems, unread badges, and 'What's New' features. Only threads from accessible categories are included. The response helps users quickly identify discussions they haven't caught up on yet, improving forum engagement and user experience.",
 *     tags={"Department Forum - Threads"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of unread threads",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Thread Title"),
 *                 @OA\Property(property="author_name", type="string", example="John Doe")
 *             ))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Patch(
 *     path="/forum/api/thread/unread/mark-as-read",
 *     summary="Mark threads as read",
 *     description="Mark all threads within a specific category as read for the authenticated user. This updates the user's read status, clearing the 'unread' indicator for all threads in that category. This is typically called when a user visits a category page or manually marks a category as read. The category_id in the request body specifies which category's threads should be marked. After this operation, those threads will no longer appear in the unread threads list until new activity occurs. This helps users track their reading progress across the forum.",
 *     tags={"Department Forum - Threads"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="category_id", type="integer", example=1, description="Category ID to mark threads as read")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Threads marked as read",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Get(
 *     path="/forum/api/thread/{thread}",
 *     summary="Get a specific thread",
 *     description="Retrieve comprehensive details of a specific thread by its ID. This endpoint returns complete thread metadata including author information, pin/lock status, reply counts, first/last post IDs, timestamps, and available actions. The response includes links to related resources such as the category, posts, and individual post endpoints. For private categories, access is verified before returning the thread. Use this endpoint when displaying thread details, building thread navigation, or when you need to check thread properties before performing operations. The thread must be accessible to the user based on category permissions.",
 *     tags={"Department Forum - Threads"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="thread",
 *         in="path",
 *         description="Thread ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Thread details",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="category_id", type="integer", example=1),
 *                 @OA\Property(property="author_id", type="integer", example=1),
 *                 @OA\Property(property="author_name", type="string", example="John Doe"),
 *                 @OA\Property(property="title", type="string", example="Thread Title"),
 *                 @OA\Property(property="pinned", type="boolean", example=false),
 *                 @OA\Property(property="locked", type="boolean", example=false),
 *                 @OA\Property(property="first_post_id", type="integer", example=1),
 *                 @OA\Property(property="last_post_id", type="integer", example=5),
 *                 @OA\Property(property="reply_count", type="integer", example=4),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time"),
 *                 @OA\Property(property="actions", type="object")
 *             ),
 *             @OA\Property(property="links", type="object",
 *                 @OA\Property(property="self", type="string", example="/forum/api/thread/1"),
 *                 @OA\Property(property="category", type="string", example="/forum/api/category/1"),
 *                 @OA\Property(property="posts", type="string", example="/forum/api/thread/1/posts")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Thread not found"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/thread/{thread}/lock",
 *     summary="Lock a thread",
 *     description="Lock a thread to prevent users from posting new replies. This endpoint requires moderator or administrator permissions. Locking is useful for closing resolved discussions, preventing spam, or maintaining important announcements. Once locked, the thread remains visible and readable, but no new posts can be added. The thread's locked status is returned in the response. Locked threads typically display a visual indicator in the UI. This is a reversible action - threads can be unlocked using the unlock endpoint. Use this for moderation purposes or to preserve important discussions.",
 *     tags={"Department Forum - Threads"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="thread",
 *         in="path",
 *         description="Thread ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Thread locked successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="locked", type="boolean", example=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/thread/{thread}/unlock",
 *     summary="Unlock a thread",
 *     description="Unlock a previously locked thread to allow users to post new replies again. This endpoint requires moderator or administrator permissions. Unlocking reverses the lock action, restoring normal posting functionality. This is useful when a discussion needs to be reopened, or if a thread was accidentally locked. The thread's locked status is updated to false and returned in the response. Users will be able to create new posts in the thread immediately after unlocking. This action only affects the ability to post - existing posts remain unchanged.",
 *     tags={"Department Forum - Threads"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="thread",
 *         in="path",
 *         description="Thread ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Thread unlocked successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="locked", type="boolean", example=false)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/thread/{thread}/pin",
 *     summary="Pin a thread",
 *     description="Pin a thread to the top of its category's thread list. This endpoint requires moderator or administrator permissions. Pinned threads appear at the top of category listings, above regular threads, regardless of their last activity time. This is ideal for important announcements, rules, FAQs, or featured discussions that should always be visible. Multiple threads can be pinned - they will appear in the order they were pinned. The pinned status is returned in the response. Pinned threads typically display a visual indicator (like a pin icon) in the UI to distinguish them from regular threads.",
 *     tags={"Department Forum - Threads"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="thread",
 *         in="path",
 *         description="Thread ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Thread pinned successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="pinned", type="boolean", example=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/thread/{thread}/unpin",
 *     summary="Unpin a thread",
 *     description="Remove the pinned status from a thread, allowing it to return to normal chronological ordering in the category listing. This endpoint requires moderator or administrator permissions. Unpinning reverses the pin action - the thread will no longer appear at the top of the list and will be sorted by its last activity time like regular threads. This is useful when an announcement is no longer relevant or when you want to remove featured status. The thread's pinned status is updated to false and returned in the response. The thread remains fully functional and accessible.",
 *     tags={"Department Forum - Threads"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="thread",
 *         in="path",
 *         description="Thread ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Thread unpinned successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="pinned", type="boolean", example=false)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/thread/{thread}/rename",
 *     summary="Rename a thread",
 *     description="Change the title of an existing thread. This endpoint can be used by the thread's original author or by moderators/administrators. The new title must be provided in the request body and must be at least 3 characters long. Renaming is useful for correcting typos, updating thread topics, or improving clarity. The thread ID, content, and all other properties remain unchanged - only the title is modified. The updated thread object is returned with the new title. This action is logged and may be visible in thread history depending on your forum configuration.",
 *     tags={"Department Forum - Threads"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="thread",
 *         in="path",
 *         description="Thread ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title"},
 *             @OA\Property(property="title", type="string", example="New Thread Title")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Thread renamed successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="New Thread Title")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error"),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/thread/{thread}/move",
 *     summary="Move a thread to another category",
 *     description="Move a thread from its current category to a different category. This endpoint requires moderator or administrator permissions. The target category ID must be provided in the request body. The target category must accept threads (accepts_threads=true). Moving a thread updates its category_id and may affect user access if the categories have different privacy settings. All posts within the thread remain intact. This is useful for organizing content, correcting misplacements, or consolidating related discussions. The updated thread object with the new category_id is returned in the response.",
 *     tags={"Department Forum - Threads"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="thread",
 *         in="path",
 *         description="Thread ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"category_id"},
 *             @OA\Property(property="category_id", type="integer", example=2, description="Target category ID")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Thread moved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="category_id", type="integer", example=2)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error"),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 * 
 * @OA\Delete(
 *     path="/forum/api/thread/{thread}",
 *     summary="Delete a thread",
 *     description="Soft delete a thread from the forum. This endpoint can be used by the thread's original author or by moderators/administrators. The thread is marked as deleted (soft delete) rather than being permanently removed, which means it can potentially be restored later. Deleting a thread also soft-deletes all posts within that thread in a cascading manner. Deleted threads are typically hidden from normal listings but may still be visible to moderators. The deleted_at timestamp is set and returned in the response. This action should be used carefully as it affects all content within the thread.",
 *     tags={"Department Forum - Threads"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="thread",
 *         in="path",
 *         description="Thread ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Thread deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="deleted_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/thread/{thread}/restore",
 *     summary="Restore a deleted thread",
 *     description="Restore a previously soft-deleted thread back to active status. This endpoint requires moderator or administrator permissions. Restoring a thread also restores all posts that were deleted along with it. The thread becomes visible again in category listings and regains full functionality. The deleted_at timestamp is cleared (set to null). This is useful for undoing accidental deletions or recovering important discussions. The restored thread object is returned with deleted_at set to null, indicating it's active again.",
 *     tags={"Department Forum - Threads"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="thread",
 *         in="path",
 *         description="Thread ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Thread restored successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="deleted_at", type="null")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 * 
 * @OA\Get(
 *     path="/forum/api/thread/{thread}/posts",
 *     summary="Get posts in a thread",
 *     description="Retrieve a paginated list of all posts within a specific thread. Posts are returned in chronological order (oldest first) by default, showing the conversation flow. The response supports pagination using the page query parameter. Each post includes author information, content, sequence number, timestamps, and available actions. For private category threads, access is verified before returning posts. This endpoint is essential for displaying the thread view page where users read and reply to discussions. The sequence field indicates the post's position in the thread (1 for the first post, 2 for the second, etc.).",
 *     tags={"Department Forum - Threads"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="thread",
 *         in="path",
 *         description="Thread ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number for pagination",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of posts",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="thread_id", type="integer", example=1),
 *                 @OA\Property(property="author_id", type="integer", example=1),
 *                 @OA\Property(property="author_name", type="string", example="John Doe"),
 *                 @OA\Property(property="content", type="string", example="Post content here..."),
 *                 @OA\Property(property="sequence", type="integer", example=1),
 *                 @OA\Property(property="created_at", type="string", format="date-time")
 *             ))
 *         )
 *     ),
 *     @OA\Response(response=404, description="Thread not found"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/thread/{thread}/posts",
 *     summary="Create a new post in a thread",
 *     description="Create a new post (reply) in an existing thread. The content field is required and must be at least 3 characters long. Optionally, you can specify a post_id to create a nested reply to a specific post. The thread must not be locked, and the user must have permission to post in the thread's category. The authenticated user automatically becomes the post author. Upon creation, the thread's last_post_id and updated_at are automatically updated to reflect the new activity. The post is assigned a sequence number based on its position in the thread. Returns the complete post object including the generated ID and all metadata.",
 *     tags={"Department Forum - Threads"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="thread",
 *         in="path",
 *         description="Thread ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"content"},
 *             @OA\Property(property="content", type="string", example="Reply content here..."),
 *             @OA\Property(property="post_id", type="integer", nullable=true, example=null, description="Parent post ID for nested replies")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="thread_id", type="integer", example=1),
 *                 @OA\Property(property="content", type="string", example="Reply content here...")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 */

/**
 * @OA\Get(
 *     path="/forum/api/post/recent",
 *     summary="Get recent posts",
 *     description="Retrieve a list of the most recently created posts across all forum threads that the user has access to. Posts are sorted by creation time (newest first). This endpoint provides a comprehensive view of recent forum activity, showing the latest contributions from all users. Only posts from accessible threads in accessible categories are included - private content is filtered based on user permissions. The response includes post content, author information, thread context, and timestamps. This is useful for activity feeds, 'Latest Posts' widgets, or monitoring forum engagement across all categories.",
 *     tags={"Department Forum - Posts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of recent posts",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="thread_id", type="integer", example=1),
 *                 @OA\Property(property="author_name", type="string", example="John Doe"),
 *                 @OA\Property(property="content", type="string", example="Post content..."),
 *                 @OA\Property(property="created_at", type="string", format="date-time")
 *             ))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Get(
 *     path="/forum/api/post/unread",
 *     summary="Get unread posts",
 *     description="Retrieve a personalized list of posts that the authenticated user hasn't read yet. Posts are considered unread if they were created or updated after the user's last read timestamp for that thread. This endpoint helps users catch up on new activity and is essential for building notification systems and unread indicators. Only posts from accessible threads are included. The response allows users to quickly identify new content they haven't seen, improving engagement and ensuring important discussions aren't missed. This complements the unread threads endpoint by providing post-level granularity.",
 *     tags={"Department Forum - Posts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of unread posts",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="thread_id", type="integer", example=1),
 *                 @OA\Property(property="author_name", type="string", example="John Doe")
 *             ))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/post/search",
 *     description="Search for posts across the entire forum using a text query. This endpoint requires the search feature to be enabled in the forum configuration. The query parameter is required and searches through post content. Optional filters allow you to narrow results by category_id (search within a specific category), author_id (posts by a specific user), or thread_id (posts within a specific thread). Search results are returned as an array of matching posts with their content, author information, and thread context. This is essential for finding specific discussions, answers, or content within the forum. The search respects user permissions - only accessible posts are included in results.",
 *     summary="Search posts",
 *     tags={"Department Forum - Posts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"query"},
 *             @OA\Property(property="query", type="string", example="search term", description="Search query string"),
 *             @OA\Property(property="category_id", type="integer", nullable=true, example=1, description="Filter by category ID"),
 *             @OA\Property(property="author_id", type="integer", nullable=true, example=1, description="Filter by author ID"),
 *             @OA\Property(property="thread_id", type="integer", nullable=true, example=1, description="Filter by thread ID")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Search results",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="thread_id", type="integer", example=1),
 *                 @OA\Property(property="content", type="string", example="Post content matching search..."),
 *                 @OA\Property(property="author_name", type="string", example="John Doe")
 *             ))
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Get(
 *     path="/forum/api/post/{post}",
 *     summary="Get a specific post",
 *     description="Retrieve complete details of a specific post by its ID. This endpoint returns comprehensive post information including content, author details, thread context, sequence number, parent post reference (if it's a nested reply), timestamps, and available actions. The response includes links to related resources such as the thread, parent post (if applicable), and self-reference. For posts in private category threads, access is verified before returning the post. Use this endpoint when displaying individual post details, building post navigation, generating permalinks, or when you need to verify post properties before performing operations.",
 *     tags={"Department Forum - Posts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="post",
 *         in="path",
 *         description="Post ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post details",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="thread_id", type="integer", example=1),
 *                 @OA\Property(property="author_id", type="integer", example=1),
 *                 @OA\Property(property="author_name", type="string", example="John Doe"),
 *                 @OA\Property(property="content", type="string", example="Post content here..."),
 *                 @OA\Property(property="sequence", type="integer", example=1),
 *                 @OA\Property(property="post_id", type="integer", nullable=true, example=null, description="Parent post ID"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time"),
 *                 @OA\Property(property="actions", type="object")
 *             ),
 *             @OA\Property(property="links", type="object",
 *                 @OA\Property(property="self", type="string", example="/forum/api/post/1"),
 *                 @OA\Property(property="thread", type="string", example="/forum/api/thread/1"),
 *                 @OA\Property(property="parent", type="string", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Post not found"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Patch(
 *     path="/forum/api/post/{post}",
 *     summary="Update a post",
 *     description="Update the content of an existing post. This endpoint can only be used by the post's original author. The content field is required and must be at least 3 characters long. Only the content can be modified - other properties like author, thread, sequence, and timestamps remain unchanged. The updated_at timestamp is automatically refreshed. This allows users to correct typos, clarify statements, or update information in their posts. The updated post object is returned with the new content and refreshed timestamp. Some forums may show an 'edited' indicator for updated posts.",
 *     tags={"Department Forum - Posts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="post",
 *         in="path",
 *         description="Post ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"content"},
 *             @OA\Property(property="content", type="string", example="Updated post content...")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="content", type="string", example="Updated post content...")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error"),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 * 
 * @OA\Delete(
 *     path="/forum/api/post/{post}",
 *     summary="Delete a post",
 *     description="Soft delete a post from a thread. This endpoint can be used by the post's original author or by moderators/administrators. The post is marked as deleted (soft delete) rather than being permanently removed, which means it can potentially be restored later. Deleting a post updates the thread's statistics (reply count decreases). Deleted posts are typically hidden from normal thread views but may still be visible to moderators. The deleted_at timestamp is set and returned in the response. If this is the first post in a thread, deleting it may affect thread visibility. Use this carefully as it removes content from the discussion.",
 *     tags={"Department Forum - Posts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="post",
 *         in="path",
 *         description="Post ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="deleted_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/post/{post}/restore",
 *     summary="Restore a deleted post",
 *     description="Restore a previously soft-deleted post back to active status. This endpoint requires moderator or administrator permissions. Restoring a post makes it visible again in thread views and restores it to normal functionality. The thread's statistics (reply count) are updated accordingly. The deleted_at timestamp is cleared (set to null). This is useful for undoing accidental deletions, recovering important content, or reversing moderation actions. The restored post object is returned with deleted_at set to null, indicating it's active again. This helps maintain discussion continuity.",
 *     tags={"Department Forum - Posts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="post",
 *         in="path",
 *         description="Post ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post restored successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="deleted_at", type="null")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 */

/**
 * @OA\Post(
 *     path="/forum/api/bulk/category/manage",
 *     summary="Bulk manage categories",
 *     description="Perform bulk administrative operations on multiple categories simultaneously. This endpoint requires administrator permissions and is designed for efficient category management. The action parameter specifies what operation to perform (e.g., 'delete', 'update'), and category_ids is an array of category IDs to operate on. This allows administrators to manage multiple categories in a single API call rather than making individual requests. Useful for batch updates, reorganizing category structures, or performing maintenance operations. Returns a success confirmation indicating the bulk operation completed. Use with caution as bulk operations affect multiple categories at once.",
 *     tags={"Department Forum - Bulk Actions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="action", type="string", example="delete", description="Action to perform"),
 *             @OA\Property(property="category_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Bulk operation completed",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Admin access required")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/bulk/thread/move",
 *     summary="Bulk move threads",
 *     description="Move multiple threads from their current categories to a target category in a single operation. This endpoint requires moderator or administrator permissions. Provide an array of thread_ids and the target category_id. All specified threads will be moved to the same destination category. This is highly efficient for reorganizing forum content, consolidating related discussions, or correcting misplacements across multiple threads. The target category must accept threads. The response includes a count of successfully moved threads. This saves significant time compared to moving threads individually and ensures consistent organization.",
 *     tags={"Department Forum - Bulk Actions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"thread_ids", "category_id"},
 *             @OA\Property(property="thread_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
 *             @OA\Property(property="category_id", type="integer", example=2, description="Target category ID")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Threads moved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="count", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error"),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/bulk/thread/lock",
 *     summary="Bulk lock threads",
 *     description="Lock multiple threads simultaneously to prevent new replies. This endpoint requires moderator or administrator permissions. Provide an array of thread_ids - all specified threads will be locked in a single operation. This is useful for closing multiple resolved discussions, preventing spam across multiple threads, or maintaining important announcements. Locked threads remain visible and readable but cannot receive new posts. The response includes a count of successfully locked threads. This is much more efficient than locking threads one by one and is essential for moderation workflows.",
 *     tags={"Department Forum - Bulk Actions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"thread_ids"},
 *             @OA\Property(property="thread_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Threads locked successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="count", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/bulk/thread/unlock",
 *     summary="Bulk unlock threads",
 *     description="Unlock multiple threads simultaneously to restore posting functionality. This endpoint requires moderator or administrator permissions. Provide an array of thread_ids - all specified threads will be unlocked in a single operation. This reverses the lock action, allowing users to post new replies again. Useful for reopening multiple discussions, correcting accidental locks, or restoring normal forum activity after maintenance. The response includes a count of successfully unlocked threads. This bulk operation saves time and ensures consistent thread management across the forum.",
 *     tags={"Department Forum - Bulk Actions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"thread_ids"},
 *             @OA\Property(property="thread_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Threads unlocked successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="count", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/bulk/thread/pin",
 *     summary="Bulk pin threads",
 *     description="Pin multiple threads to the top of their respective category listings simultaneously. This endpoint requires moderator or administrator permissions. Provide an array of thread_ids - all specified threads will be pinned in a single operation. Pinned threads appear at the top of category lists, above regular threads. This is ideal for featuring multiple important announcements, rules, or discussions across different categories. The response includes a count of successfully pinned threads. Bulk pinning is much more efficient than pinning threads individually and ensures consistent visibility for important content.",
 *     tags={"Department Forum - Bulk Actions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"thread_ids"},
 *             @OA\Property(property="thread_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Threads pinned successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="count", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/bulk/thread/unpin",
 *     summary="Bulk unpin threads",
 *     description="Remove the pinned status from multiple threads simultaneously, allowing them to return to normal chronological ordering. This endpoint requires moderator or administrator permissions. Provide an array of thread_ids - all specified threads will be unpinned in a single operation. Unpinned threads will be sorted by their last activity time like regular threads. Useful when multiple announcements are no longer relevant or when removing featured status from several threads. The response includes a count of successfully unpinned threads. This bulk operation streamlines content management and category organization.",
 *     tags={"Department Forum - Bulk Actions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"thread_ids"},
 *             @OA\Property(property="thread_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Threads unpinned successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="count", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 * 
 * @OA\Delete(
 *     path="/forum/api/bulk/thread",
 *     summary="Bulk delete threads",
 *     description="Soft delete multiple threads simultaneously. This endpoint requires moderator or administrator permissions. Provide an array of thread_ids in the request body - all specified threads will be deleted in a single operation. This is a soft delete operation, meaning threads can potentially be restored later. Deleting threads also soft-deletes all posts within those threads in a cascading manner. The response includes a count of successfully deleted threads. Use this with extreme caution as it affects all content within the specified threads. This bulk operation is essential for efficient moderation and content cleanup workflows.",
 *     tags={"Department Forum - Bulk Actions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"thread_ids"},
 *             @OA\Property(property="thread_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Threads deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="count", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/bulk/thread/restore",
 *     summary="Bulk restore threads",
 *     description="Restore multiple previously soft-deleted threads back to active status simultaneously. This endpoint requires moderator or administrator permissions. Provide an array of thread_ids - all specified threads will be restored in a single operation. Restoring threads also restores all posts that were deleted along with them. The threads become visible again in category listings and regain full functionality. The response includes a count of successfully restored threads. This is useful for undoing accidental bulk deletions, recovering important discussions, or reversing moderation actions. Bulk restoration saves significant time compared to restoring threads individually.",
 *     tags={"Department Forum - Bulk Actions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"thread_ids"},
 *             @OA\Property(property="thread_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Threads restored successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="count", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 * 
 * @OA\Delete(
 *     path="/forum/api/bulk/post",
 *     summary="Bulk delete posts",
 *     description="Soft delete multiple posts simultaneously from various threads. This endpoint requires moderator or administrator permissions. Provide an array of post_ids in the request body - all specified posts will be deleted in a single operation. This is a soft delete operation, meaning posts can potentially be restored later. Deleting posts updates the thread statistics (reply counts decrease accordingly). Deleted posts are typically hidden from normal thread views. The response includes a count of successfully deleted posts. Use this carefully as it removes content from discussions. This bulk operation is essential for efficient moderation, spam removal, and content cleanup across multiple threads.",
 *     tags={"Department Forum - Bulk Actions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"post_ids"},
 *             @OA\Property(property="post_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Posts deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="count", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 * 
 * @OA\Post(
 *     path="/forum/api/bulk/post/restore",
 *     summary="Bulk restore posts",
 *     description="Restore multiple previously soft-deleted posts back to active status simultaneously. This endpoint requires moderator or administrator permissions. Provide an array of post_ids - all specified posts will be restored in a single operation. Restored posts become visible again in thread views and regain full functionality. Thread statistics (reply counts) are updated accordingly. The response includes a count of successfully restored posts. This is useful for undoing accidental bulk deletions, recovering important content, or reversing moderation actions across multiple threads. Bulk restoration is much more efficient than restoring posts individually and helps maintain discussion continuity.",
 *     tags={"Department Forum - Bulk Actions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"post_ids"},
 *             @OA\Property(property="post_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Posts restored successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="count", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Moderator access required")
 * )
 */
class ForumApiDocumentation
{
    // This class only holds the OpenAPI annotations for Forum APIs
}
