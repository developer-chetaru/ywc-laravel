<?php

use App\Livewire\Forum\Pages\Category\CategoryIndex;
use App\Livewire\Forum\Pages\Category\CategoryEdit;
use App\Livewire\Forum\Pages\Thread\CreateThread;
use App\Livewire\Forum\Pages\Thread\ThreadShow;
use App\Livewire\Forum\Pages\Post\PostEdit;
use TeamTeaTime\Forum\Http\Livewire\Pages\{
    CategoryCreate,
    CategoryShow,
    PostShow,
    RecentThreads,
    ThreadReply,
    UnreadThreads,
    UpdateCategoryTree,
};

$prefix = config('forum.frontend.route_prefixes');

Route::get('/', CategoryIndex::class)->name('category.index');
Route::get('category/order', UpdateCategoryTree::class)->name('category.order');
Route::get('category/create', \App\Livewire\Forum\Pages\Category\CreateForum::class)->name('category.create');

Route::get('recent', RecentThreads::class)->name('recent');
Route::get('unread', UnreadThreads::class)->name('unread');
Route::get('leaderboard', \App\Livewire\Forum\Leaderboard::class)->name('leaderboard');
Route::get('moderator/dashboard', \App\Livewire\Forum\ModeratorDashboard::class)->name('moderator.dashboard');
Route::get('search', \App\Livewire\Forum\SearchResults::class)->name('search');
Route::get('notifications', \App\Livewire\Forum\NotificationsPage::class)->name('notifications.index');
Route::get('notifications/preferences', \App\Livewire\Forum\NotificationPreferences::class)->name('notifications.preferences');

// Private Messages
Route::get('messages', \App\Livewire\Forum\MessageList::class)->name('messages.index');
Route::get('messages/send', \App\Livewire\Forum\SendMessage::class)->name('messages.send');
Route::get('messages/{messageId}', \App\Livewire\Forum\MessageConversation::class)->name('messages.conversation');

Route::group(['prefix' => $prefix['category'] . '/{category_id}-{category_slug}'], function () use ($prefix) {
    Route::get('/', CategoryShow::class)->name('category.show');
    Route::get('edit', CategoryEdit::class)->name('category.edit');
    Route::get($prefix['thread'] . '/create', CreateThread::class)->name('thread.create');
});

Route::group(['prefix' => $prefix['thread'] . '/{thread_id}-{thread_slug}'], function () use ($prefix) {
    Route::get('/', \App\Livewire\Forum\Pages\Thread\ThreadShow::class)->name('thread.show');
    Route::get('reply', ThreadReply::class)->name('thread.reply');
    Route::get($prefix['post'] . '/{post_id}/edit', \App\Livewire\Forum\Pages\Post\PostEdit::class)->name('post.edit');
    Route::get($prefix['post'] . '/{post_id}', PostShow::class)->name('post.show');
});

