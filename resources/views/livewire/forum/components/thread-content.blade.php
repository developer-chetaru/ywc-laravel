<div class="bg-white rounded-md shadow-md p-6 dark:bg-slate-700">
    <h1 class="text-xl font-bold mb-4">{{ $thread->title }}</h1>

    <div class="prose max-w-none dark:prose-invert">
        {!! $thread->firstPost->content !!}
    </div>

    <div class="mt-6 text-sm text-gray-500">
        Posted by {{ $thread->author->name }} 
        on {{ $thread->created_at->format('M d, Y H:i') }}
    </div>
</div>
