<div id="post-{{ $post->sequence }}" class="post-card my-4" x-data="postCard" data-post="{{ $post->id }}" {{ $selectable ? 'x-on:change=onPostChanged' : '' }}>
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg flex flex-col sm:flex-row items-stretch {{ $post->trashed() ? 'opacity-65' : '' }}" :class="classes">
        @if ($showAuthorPane)
            <div class="flex flex-row sm:flex-col w-full sm:w-1/5 px-6 py-4 sm:py-6 border-b sm:border-b-0 sm:border-r border-gray-200 bg-gray-50">
                <div class="grow">
                    <div class="text-lg font-medium text-gray-800 truncate">
                        {{ $post->authorName }}
                    </div>
                    @php
                        $author = $post->author ?? null;
                        if ($author) {
                            $reputationService = app(\App\Services\Forum\ForumReputationService::class);
                            $points = $author->forum_reputation_points ?? 0;
                            $level = $reputationService->getReputationLevel($points);
                            $levelColor = $reputationService->getReputationLevelColor($level);
                            
                            // Get user badges
                            $badgeService = app(\App\Services\Forum\BadgeService::class);
                            $badges = $badgeService->getUserBadges($author);
                        }
                    @endphp
                    @if ($author && isset($points) && isset($level))
                        <div class="mt-2 space-y-2">
                            <div class="flex flex-wrap gap-2 items-center">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border {{ $levelColor }}">
                                    {{ $level }}
                                </span>
                                <span class="text-xs text-gray-600">
                                    {{ $points }} pts
                                </span>
                            </div>
                            @if (isset($badges) && $badges->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($badges->take(3) as $badge)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 border border-blue-300" 
                                              title="{{ $badge->description }}">
                                            {{ $badge->name }}
                                        </span>
                                    @endforeach
                                    @if ($badges->count() > 3)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-300">
                                            +{{ $badges->count() - 3 }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="text-gray-600">
                    @if (! isset($single) || ! $single)
                        <a href="{{ Forum::route('thread.show', $post) }}" class="text-blue-600 hover:text-blue-700">#{{ $post->sequence }}</a>
                    @endif
                </div>
            </div>
        @endif
        <div class="grow p-6 w-full sm:w-4/5">
            @if (isset($post->parent))
                <livewire:forum::components.post.quote :post="$post->parent" />
            @endif

            <div class="text-gray-900">
                @if ($post->trashed())
                    @can ('viewTrashedPosts')
                        <div class="mb-4">
                            {!! Forum::render($post->content) !!}
                        </div>
                    @endcan

                    <div>
                        <livewire:forum::components.pill
                            bg-color="bg-zinc-400"
                            text-color="text-zinc-950"
                            margin="mr-2"
                            icon="trash-mini"
                            :text="trans('forum::general.deleted')" />
                    </div>
                @else
                    {!! Forum::render($post->content) !!}
                @endif
            </div>

            {{-- Post Reactions --}}
            @if (!$post->trashed())
                <div class="flex items-center justify-between">
                    <livewire:forum.post-reactions :post="$post" />
                    @if($post->thread)
                        <livewire:forum.mark-best-answer :thread="$post->thread" :post="$post" />
                    @endif
                </div>
            @endif

            <div class="flex flex-col sm:flex-row mt-4">
                <div class="grow text-slate-500">
                    <livewire:forum::components.timestamp :carbon="$post->created_at" />
                    @if ($post->hasBeenUpdated())
                        <span class="mx-1 text-slate-500">•</span>
                        {{ trans('forum::general.last_updated') }} <livewire:forum::components.timestamp :carbon="$post->updated_at" />
                    @endif
                </div>
                @if (!isset($single) || !$single)
                    <div class="text-right sm:text-left mt-2 sm:mt-0">
                        @if (!$post->trashed())
                            <!-- <a href="{{ Forum::route('post.show', $post) }}" class="font-medium">
                                {{ trans('forum::general.permalink') }}
                            </a> -->
                            @can ('edit', $post)
                                <a href="{{ Forum::route('post.edit', $post) }}" class="font-medium ml-2">
                                    {{ trans('forum::general.edit') }}
                                </a>
                            @endcan
                            @can ('reply', $post->thread)
                                <a href="{{ Forum::route('thread.reply', $post->thread) }}?parent_id={{ $post->id }}" class="font-medium ml-2">
                                    {{ trans('forum::general.reply') }}
                                </a>
                            @endcan
                            @if (!$post->trashed())
                                <div class="flex gap-2">
                                    @if ($author && Auth::check() && Auth::id() !== $author->id)
                                        <livewire:forum.send-message :recipient-id="$author->id" :recipient-name="$author->first_name . ' ' . $author->last_name" />
                                    @endif
                                    <livewire:forum.report-content reportable-type="post" :reportable-id="$post->id" />
                                </div>
                            @endif
                        @endif
                        @if ($selectable)
                            <div class="inline-block ml-4">
                                <x-forum::form.input-checkbox
                                    id=""
                                    :value="$post->id"
                                    @change="onChanged" />
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


@script
<script>
Alpine.data('postCard', () => {
    return {
        classes: 'outline-none',
        onChanged(event) {
            event.stopPropagation();

            if (event.target.checked) {
                this.classes = 'outline outline-blue-500';
            } else {
                this.classes = 'outline-none';
            }

            $dispatch('change', { isSelected: event.target.checked, id: event.target.value });
        }
    }
});
</script>
@endscript
