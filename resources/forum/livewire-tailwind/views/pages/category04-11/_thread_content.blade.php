@if($selectedThread)
    <h1 class="text-2xl font-bold">{{ $selectedThread->title }}</h1>
    <p class="text-gray-500 text-sm">Posted by {{ $selectedThread->user->name ?? 'Unknown' }} on {{ $selectedThread->created_at->format('M d, Y') }}</p>
    
    <div class="mt-4 text-gray-800">
        {!! nl2br(e($selectedThread->content)) !!}
    </div>

    <h3 class="mt-6 font-semibold">Posts:</h3>
    <ul class="space-y-4 mt-2">
        @foreach($selectedThread->posts as $post)
            <li class="p-3 bg-gray-100 rounded">
                <p>{{ $post->body ?? $post->content }}</p>
                <small class="text-gray-500">Posted by {{ $post->user->name ?? 'Unknown' }} on {{ $post->created_at->format('M d, Y') }}</small>
            </li>
        @endforeach
    </ul>
@else
    <div class="flex flex-col items-center justify-center h-full text-center">
        <div class="bg-blue-600 p-4 rounded-full mb-6 shadow-lg">
            <img src="{{ asset('images/wechat.svg') }}" class="w-12 h-12 text-white" alt="WeChat Icon" />
        </div>
        <h1 class="text-3xl font-semibold text-blue-600 mb-2">Welcome to Department Forums</h1>
        <p class="text-gray-600 mb-6">
            Select a forum category from the list on the left to view threads and discussions.
        </p>
    </div>
@endif
