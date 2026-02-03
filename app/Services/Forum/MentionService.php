<?php

namespace App\Services\Forum;

use App\Models\User;
use Illuminate\Support\Str;

class MentionService
{
    /**
     * Extract mentioned usernames from content
     * Supports @username and @firstname lastname formats
     */
    public function extractMentions(string $content): array
    {
        $mentions = [];
        
        // Pattern 1: @username (single word after @)
        preg_match_all('/@(\w+)/', $content, $usernameMatches);
        if (!empty($usernameMatches[1])) {
            $mentions = array_merge($mentions, $usernameMatches[1]);
        }
        
        // Pattern 2: @Firstname Lastname (two words after @)
        preg_match_all('/@([A-Z][a-z]+)\s+([A-Z][a-z]+)/', $content, $nameMatches);
        if (!empty($nameMatches[1]) && !empty($nameMatches[2])) {
            for ($i = 0; $i < count($nameMatches[1]); $i++) {
                $firstName = $nameMatches[1][$i];
                $lastName = $nameMatches[2][$i];
                $mentions[] = $firstName . ' ' . $lastName;
            }
        }
        
        return array_unique($mentions);
    }

    /**
     * Find users by mentions
     */
    public function findMentionedUsers(array $mentions): array
    {
        $users = [];
        
        foreach ($mentions as $mention) {
            // Try to find by username (if it's a single word)
            if (strpos($mention, ' ') === false) {
                $user = User::where('email', 'like', $mention . '%')
                    ->orWhere('first_name', 'like', $mention . '%')
                    ->orWhere('last_name', 'like', $mention . '%')
                    ->first();
            } else {
                // Try to find by first name and last name
                $parts = explode(' ', $mention, 2);
                if (count($parts) === 2) {
                    $user = User::where('first_name', 'like', $parts[0] . '%')
                        ->where('last_name', 'like', $parts[1] . '%')
                        ->first();
                } else {
                    continue;
                }
            }
            
            if ($user) {
                $users[] = $user;
            }
        }
        
        return array_unique($users);
    }

    /**
     * Process mentions in content and return mentioned users
     */
    public function processMentions(string $content): array
    {
        $mentions = $this->extractMentions($content);
        return $this->findMentionedUsers($mentions);
    }
}
