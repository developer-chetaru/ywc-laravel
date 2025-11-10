<div>
    @foreach ($posts as $post)
        <div class="mb-4 p-4 bg-gray-100 rounded">
            <p class="text-sm text-gray-600">
                <strong>{{ $post->author->name }}</strong> 
                • {{ $post->created_at->diffForHumans() }}
            </p>
            <p class="mt-2 text-gray-800">{{ $post->content }}</p>
        </div>
    @endforeach
</div>