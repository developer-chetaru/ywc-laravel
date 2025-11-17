<div x-data="thread" x-on:page-changed="onPageChanged">
    <div class="flex justify-between items-center">
        {{-- Thread Title --}}
        <h1 class="text-2xl font-semibold">{{ $thread->title }}</h1>

        {{-- Back Button on the right --}}
        <x-forum::link-button
            :href="route('forum.category.index')"
            :label="'← Back'"
            class="link-button inline-block rounded-md font-medium text-l text-center text-white bg-blue-600 hover:text-white hover:bg-blue-500 px-4 py-2" />
    </div>

    <div class="flex items-center">
        <div class="grow">
            @if ($thread->pinned)
                <livewire:forum::components.pill
                    bg-color="bg-amber-400"
                    text-color="text-amber-950"
                    margin="mr-2"
                    icon="arrow-up-circle-mini"
                    :text="trans('forum::threads.pinned')" />
            @endif
            @if ($thread->locked)
                <livewire:forum::components.pill
                    bg-color="bg-rose-400"
                    text-color="text-rose-950"
                    margin="mr-2"
                    icon="lock-closed-mini"
                    :text="trans('forum::threads.locked')" />
            @endif
            @if ($thread->trashed())
                <livewire:forum::components.pill
                    bg-color="bg-zinc-400"
                    text-color="text-zinc-950"
                    margin="mr-2"
                    icon="trash-mini"
                    :text="trans('forum::general.deleted')" />
            @endif
        </div>

    </div>

    <div class="flex flex-col lg:flex-row items-center">
        <div class="grow">
            <div class="inline-flex flex-wrap sm:flex-nowrap">
                @if (Gate::allows('deleteThreads', $thread->category) && Gate::allows('delete', $thread))
                    @if ($thread->trashed())
                        <x-forum::group-button
                            intent="danger"
                            size="small"
                            icon="trash-mini"
                            href="#"
                            :label="trans('forum::general.perma_delete')"
                            @click.prevent="confirmThreadAction('permadelete', '{{ trans_choice('forum::threads.confirm_perma_delete', 1) }}')" />
                    @else
                        <x-forum::group-button
                            intent="danger"
                            size="small"
                            icon="trash-mini"
                            href="#"
                            :label="trans('forum::general.delete')"
                            @click.prevent="confirmThreadAction('delete', '{{ trans_choice('forum::threads.confirm_delete', 1) }}')" />
                    @endif
                @endif
                @if ($thread->trashed() && Gate::allows('restoreThreads', $thread->category) && Gate::allows('restore', $thread))
                    <x-forum::group-button
                        intent="secondary"
                        size="small"
                        icon="arrow-path-mini"
                        :label="trans('forum::general.restore')"
                        @click.prevent="confirmThreadAction('restore', '{{ trans_choice('forum::threads.confirm_restore', 1) }}')" />
                @endif
                @if (!$thread->trashed())
                    
                    @can ('rename', $thread)
                        <x-forum::group-button
                            intent="secondary"
                            size="small"
                            icon="pencil-mini"
                            :label="trans('forum::general.rename')"
                            @click.prevent="confirmThreadAction('rename', '')" />
                    @endcan
                    
                @endif
            </div>
        </div>
        @if (!$thread->trashed())
            @can ('reply', $thread)
                <div class="inline-flex gap-x-2 mt-4 lg:mt-0">
                    <x-forum::link-button
                        intent="secondary"
                        href="#quick-reply"
                        :label="trans('forum::general.quick_reply')" />

                    <x-forum::link-button
                        intent="primary"
                        :href="Forum::route('thread.reply', $thread)"
                        :label="trans('forum::general.reply')" />
                </div>
            @endcan
        @endif
    </div>

    <div>
        <livewire:forum.components.thread-content :thread="$thread" />
    </div>

    <div>
        @foreach ($posts as $post)
            <livewire:forum::components.post.card
                :$post
                :key="$post->id . $updateKey"
                :selectable="false" />
        @endforeach
    </div>

    {{ $posts->links('forum::components.pagination') }}


    <div>
        {{-- Thread content --}}
        

        {{-- Quick Reply --}}
        @if (!$thread->trashed() && auth()->check())
            <livewire:forum.quick-reply :thread="$thread" wire:key="quick-reply-{{ $updateKey }}" />
        @endif
    </div>




    


    <x-forum::modal x-show="showThreadActionConfirmationModal" :heading="trans('forum::general.confirm_action')" onClose="showThreadActionConfirmationModal = false">
        <span x-text="threadActionConfirmationText"></span>

        <div x-show="threadAction == 'rename'">
            <x-forum::form.input-text
                id="title"
                :label="trans('forum::general.title')"
                wire:model="threadEditForm.title" />
        </div>

        <div x-show="threadAction == 'move'">
            <x-forum::form.input-select
                id="destination-category"
                :label="trans('forum::general.move_to')"
                wire:model="destinationCategoryId">
                <option value="0" disabled>...</option>
                @include ('forum::components.category.options', ['categories' => $threadDestinationCategories, 'disable' => $thread->category->id])
            </x-forum::form.input-select>
        </div>

        <div class="flex flex-wrap mt-6">
            <div class="grow">
                <x-forum::link-button
                    intent="secondary"
                    :label="trans('forum::general.cancel')"
                    @click.prevent="showThreadActionConfirmationModal = false" />
            </div>
            <div>
                <x-forum::button
                    type="submit"
                    :label="trans('forum::general.proceed')"
                    @click="applyThreadAction" />
            </div>
        </div>
    </x-forum::modal>

    <x-forum::modal x-show="showPostsActionConfirmationModal" :heading="trans('forum::general.confirm_action')" onClose="showPostsActionConfirmationModal = false">
        {{ trans('forum::general.generic_confirm') }}

        <div class="flex flex-wrap mt-6">
            <div class="grow">
                <x-forum::link-button
                    intent="secondary"
                    :label="trans('forum::general.cancel')"
                    @click.prevent="showPostsActionConfirmationModal = false" />
            </div>
            <div>
                <x-forum::button
                    type="submit"
                    :label="trans('forum::general.proceed')"
                    @click="applyPostsAction" />
            </div>
        </div>
    </x-forum::modal>
</div>

@script
<script>
Alpine.data('thread', () => {
    return {
        toggledAllPosts: false,
        selectedPosts: [],
        postsAction: 'none',
        permadeletePosts: false,
        showPostsActionConfirmationModal: false,
        threadAction: null,
        showThreadActionConfirmationModal: false,
        threadActionConfirmationText: '',
        reset() {
            this.toggledAllPosts = false;
            this.selectedPosts = [];
            this.showThreadActionConfirmationModal = false;
            this.showPostsActionConfirmationModal = false;
        },
        onPostChanged(event) {
            if (event.detail.isSelected) {
                this.selectedPosts.push(event.detail.id);
            } else {
                this.selectedPosts.splice(this.selectedPosts.indexOf(event.detail.id), 1);
            }
        },
        onPageChanged() {
            this.reset();
        },
        confirmThreadAction(action, text) {
            this.threadAction = action;
            this.threadActionConfirmationText = text;
            this.showThreadActionConfirmationModal = true;
        },
        async applyThreadAction() {
            let result;
            switch (this.threadAction) {
                case 'delete':
                    result = await $wire.delete(false);
                    break;
                case 'permadelete':
                    result = await $wire.delete(true);
                    break;
                case 'restore':
                    result = await $wire.restore();
                    break;
                case 'lock':
                    result = await $wire.lock();
                    break;
                case 'unlock':
                    result = await $wire.unlock();
                    break;
                case 'pin':
                    result = await $wire.pin();
                    break;
                case 'unpin':
                    result = await $wire.unpin();
                    break;
                case 'rename':
                    result = await $wire.rename();
                    break;
                case 'move':
                    result = await $wire.move();
                    break;
            }

            if (result === null) return;
            if (result.type == 'success') this.showThreadActionConfirmationModal = false;
            $dispatch('alert', result);
        },
        confirmPostsAction() {
            this.showPostsActionConfirmationModal = true;
        },
        async applyPostsAction() {
            if (this.postsAction == null || this.selectedPosts.length == 0) {
                return;
            }

            let result;
            switch (this.postsAction) {
                case 'delete':
                    result = await $wire.deletePosts(this.selectedPosts, this.permadeletePosts);
                    break;
                case 'restore':
                    result = await $wire.restorePosts(this.selectedPosts);
                    break;
            }

            if (result.type == 'success') this.reset();
            $dispatch('alert', result);
        },
        async reply() {
            const result = await $wire.reply();
            if (result === null) return;
            if (result.type == 'success') this.reset();
            $dispatch('alert', result);
        },
        toggleAllPosts() {
            this.toggledAllPosts = !this.toggledAllPosts;
            if (!this.toggledAllPosts) this.selectedPosts = [];
            const checkboxes = document.querySelectorAll('[data-post] input[type=checkbox]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.toggledAllPosts;
                checkbox.dispatchEvent(new Event('change'));
            });
        }
    }
});
</script>
@endscript
