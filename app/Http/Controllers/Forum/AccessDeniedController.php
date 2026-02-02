<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Services\Forum\ForumRoleAccessService;
use Illuminate\Http\Request;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class AccessDeniedController extends Controller
{
    protected ForumRoleAccessService $roleAccessService;

    public function __construct(ForumRoleAccessService $roleAccessService)
    {
        $this->roleAccessService = $roleAccessService;
    }

    public function category($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $requiredRoles = $this->roleAccessService->getCategoryRoles($categoryId);

        return view('forum.access-denied', [
            'type' => 'category',
            'requiredRoles' => $requiredRoles,
        ]);
    }

    public function thread($threadId)
    {
        $thread = Thread::findOrFail($threadId);
        $requiredRoles = $this->roleAccessService->getThreadRoles($threadId);

        return view('forum.access-denied', [
            'type' => 'thread',
            'requiredRoles' => $requiredRoles,
        ]);
    }
}
