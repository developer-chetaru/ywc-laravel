<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use TeamTeaTime\Forum\Models\Post;

class ForumPostQuote extends Model
{
    protected $fillable = [
        'quoted_post_id',
        'quoting_post_id',
        'quoted_content',
    ];

    /**
     * Get the post that was quoted
     */
    public function quotedPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'quoted_post_id');
    }

    /**
     * Get the post that contains the quote
     */
    public function quotingPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'quoting_post_id');
    }
}
