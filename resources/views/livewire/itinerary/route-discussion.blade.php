<div class="bg-white border border-gray-200 rounded-lg p-6 space-y-5 shadow-sm">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Crew Discussion</h2>
            <p class="text-sm text-gray-500">Keep everyone aligned with updates, notes, and decisions for each leg of the voyage.</p>
        </div>
    </div>

    @if($alert)
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-md text-sm">
            {{ $alert }}
        </div>
    @endif

    <form wire:submit.prevent="postComment" class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-3">
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <div class="flex-1">
                <label class="block text-xs uppercase tracking-wide text-gray-500">Message</label>
                <textarea wire:model.defer="form.body" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                          placeholder="Share an update or ask the crew a question..."></textarea>
                @error('form.body') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="w-full md:w-40">
                <label class="block text-xs uppercase tracking-wide text-gray-500">Visibility</label>
                <select wire:model.defer="form.visibility"
                        class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="crew">Crew only</option>
                    <option value="public">Public</option>
                </select>
            </div>
        </div>
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <div class="w-full md:w-56">
                <label class="block text-xs uppercase tracking-wide text-gray-500">Related stop</label>
                <select wire:model.defer="form.stop_id"
                        class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">General</option>
                    @foreach($stops as $stop)
                        <option value="{{ $stop->id }}">Stop {{ $stop->sequence }} — {{ $stop->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 flex items-center gap-2">
                @if($replyingTo)
                    <span class="text-xs text-gray-600">Replying to comment #{{ $replyingTo }}</span>
                    <button type="button" wire:click="cancelReply" class="text-xs text-indigo-600 hover:underline">Cancel reply</button>
                @endif
            </div>
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md shadow hover:bg-indigo-700">
                Post
            </button>
        </div>
    </form>

    <div class="space-y-4">
        @forelse($comments as $comment)
            <div class="border border-gray-200 rounded-lg p-4 bg-white space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold">
                            {{ strtoupper(substr(optional($comment->user)->first_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ optional($comment->user)->first_name }} {{ optional($comment->user)->last_name }}
                                <span class="text-xs text-gray-500">• {{ $comment->created_at->diffForHumans() }}</span>
                            </p>
                            <p class="text-xs text-gray-500">
                                Visibility: <span class="font-medium text-indigo-600">{{ ucfirst($comment->visibility) }}</span>
                                @if($comment->stop)
                                    • Stop {{ $comment->stop->sequence }} ({{ $comment->stop->name }})
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <button wire:click="startReply({{ $comment->id }})" class="text-indigo-600 hover:underline">Reply</button>
                        @if($comment->user_id === auth()->id() || auth()->user()?->can('manageCrew', $route))
                            <button wire:click="deleteComment({{ $comment->id }})" class="text-red-600 hover:underline">Delete</button>
                        @endif
                    </div>
                </div>
                <div class="text-sm text-gray-700 whitespace-pre-line">
                    {{ $comment->body }}
                </div>

                @if($comment->children->isNotEmpty())
                    <div class="border-l-2 border-gray-200 pl-4 space-y-3">
                        @foreach($comment->children as $child)
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span class="font-medium text-gray-700">{{ optional($child->user)->first_name }} {{ optional($child->user)->last_name }}</span>
                                    <span>{{ $child->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ $child->body }}</p>
                                <div class="flex items-center justify-between text-xs text-gray-500 mt-2">
                                    <span>Visibility: {{ ucfirst($child->visibility) }}</span>
                                    @if($child->user_id === auth()->id() || auth()->user()?->can('manageCrew', $route))
                                        <button wire:click="deleteComment({{ $child->id }})" class="text-red-600 hover:underline">Delete</button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-500">No crew updates yet. Start the conversation above.</p>
        @endforelse
    </div>

    <div>
        {{ $comments->links() }}
    </div>
</div>

