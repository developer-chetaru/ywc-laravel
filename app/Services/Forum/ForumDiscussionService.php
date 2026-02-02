<?php

namespace App\Services\Forum;

use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Models\Thread;

class ForumDiscussionService
{
    /**
     * Check if an active discussion exists for a source item
     * 
     * @param string $module
     * @param int $itemId
     * @param string|null $itemType
     * @return Thread|null
     */
    public function getActiveDiscussion(string $module, int $itemId, ?string $itemType = null): ?Thread
    {
        $query = Thread::where('source_module', $module)
            ->where('source_item_id', $itemId)
            ->whereNull('deleted_at'); // Not soft deleted

        if ($itemType) {
            $query->where('source_item_type', $itemType);
        }

        return $query->first();
    }

    /**
     * Check if an active discussion exists (boolean)
     * 
     * @param string $module
     * @param int $itemId
     * @param string|null $itemType
     * @return bool
     */
    public function hasActiveDiscussion(string $module, int $itemId, ?string $itemType = null): bool
    {
        return $this->getActiveDiscussion($module, $itemId, $itemType) !== null;
    }

    /**
     * Get discussion URL for a source item
     * 
     * @param string $module
     * @param int $itemId
     * @param string|null $itemType
     * @return string|null
     */
    public function getDiscussionUrl(string $module, int $itemId, ?string $itemType = null): ?string
    {
        $thread = $this->getActiveDiscussion($module, $itemId, $itemType);
        return $thread ? $thread->route : null;
    }

    /**
     * Get discussion count for a source item
     * 
     * @param string $module
     * @param int $itemId
     * @param string|null $itemType
     * @return int
     */
    public function getDiscussionCount(string $module, int $itemId, ?string $itemType = null): int
    {
        $query = Thread::where('source_module', $module)
            ->where('source_item_id', $itemId)
            ->whereNull('deleted_at');

        if ($itemType) {
            $query->where('source_item_type', $itemType);
        }

        return $query->count();
    }
}
