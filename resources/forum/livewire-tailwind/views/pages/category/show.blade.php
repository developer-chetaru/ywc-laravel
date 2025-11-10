<div x-data="category" x-on:page-changed="onPageChanged" style="{{ $category->styleVariables }}">
    <div class="flex justify-between items-center mt-4 mb-6">
        <div>
            <h1 class="mb-0 text-category text-2xl font-bold">{{ $category->title }}</h1>
            <h2 class="mt-0 text-slate-500">{{ $category->description }}</h2>
        </div>

        <div class="flex space-x-2">
            {{-- Edit button (left side) --}}
            
            @can ('edit', $category)
                <x-forum::link-button
                    intent="secondary"
                    :href="Forum::route('category.edit', $category)"
                    :label="trans('forum::categories.edit')" 
                    class="link-button inline-block flex justify-center items-center rounded-md font-medium text-l text-center text-zinc-800 bg-zinc-400/50 hover:bg-zinc-400/35 dark:text-slate-800 dark:bg-slate-400/50 dark:hover:bg-slate-400/65 min-w-36 px-4 py-2" />
            @endcan

            {{-- Create Category button --}}
            @role('super_admin')
                @can ('createCategories')
                    <x-forum::link-button
                        :label="trans('forum::categories.create')"
                        icon="squares-plus-outline"
                        :href="Forum::route('category.create') . '?parent_id=' . $category->id"
                        class="link-button inline-block rounded-md font-medium text-l text-center text-white bg-blue-600 hover:text-white hover:bg-blue-500 min-w-36 px-4 py-2" />
                @endcan
            @endrole

            {{-- New Thread button --}}
            @if ($category->accepts_threads)
                <x-forum::link-button
                    :href="Forum::route('thread.create', $category)"
                    icon="pencil-outline"
                    :label="trans('forum::threads.new_thread')" 
                    class="link-button inline-block rounded-md font-medium text-l text-center text-white bg-blue-600 hover:text-white hover:bg-blue-500 min-w-36 px-4 py-2"/>
            @endif

            {{-- Back Button --}}
            <x-forum::link-button
                :href="route('forum.category.index')"
                :label="'← Back'"
                class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md shadow-md" 
                class="link-button inline-block rounded-md font-medium text-l text-center text-white bg-blue-600 hover:text-white hover:bg-blue-500 min-w-36 px-4 py-2"/>
        </div>
    </div>

    {{-- Category descendants --}}
    @foreach ($category->descendants as $child)
        <livewire:forum::components.category.card :category="$child" :key="$child->id" />
    @endforeach

    {{-- Threads select all checkbox --}}
    @if (count($selectableThreadIds) > 0)
        <div class="flex justify-end mb-4">
            <x-forum::form.input-checkbox
                id="toggle-all"
                value=""
                :label="trans('forum::threads.select_all')"
                x-model="toggledAllThreads"
                @click="toggleAllThreads" />
        </div>
    @endif

    {{-- Threads list --}}
    <div class="my-4">
        @foreach ($threads as $thread)
            <livewire:forum::components.thread.card
                :$thread
                :key="$thread->id . $updateKey"
                :selectable="in_array($thread->id, $selectableThreadIds)" />
        @endforeach

        @if ($category->accepts_threads && $threads->count() == 0)
            <div class="p-6 border border-slate-300 dark:border-slate-700 rounded-md text-center text-slate-500 text-lg font-medium">
                {{ trans('forum::threads.none_found') }}
            </div>
        @endif
    </div>

    {{-- Action panel for selected threads --}}
    <div x-show="selectedThreads.length > 0" class="fixed bottom-0 right-0 z-40 min-w-96 bg-white shadow-md rounded-md m-4 p-6 dark:bg-slate-700">
        <h3>{{ trans('forum::general.with_selection') }}</h3>

        <x-forum::form.input-select
            id="selected-action"
            x-model="selectedAction">
                <option value="none" disabled>{{ trans_choice('forum::general.actions', 1) }}...</option>
            @can ('deleteThreads', $category)
                <option value="delete">{{ trans('forum::general.delete') }}</option>
            @endcan
            @can ('restoreThreads', $category)
                <option value="restore">{{ trans('forum::general.restore') }}</option>
            @endcan
            @can ('moveThreadsFrom', $category)
                <option value="move">{{ trans('forum::general.move') }}</option>
            @endcan
            @can ('lockThreads', $category)
                <option value="lock">{{ trans('forum::threads.lock') }}</option>
                <option value="unlock">{{ trans('forum::threads.unlock') }}</option>
            @endcan
            @can ('pinThreads', $category)
                <option value="pin">{{ trans('forum::threads.pin') }}</option>
                <option value="unpin">{{ trans('forum::threads.unpin') }}</option>
            @endcan
        </x-forum::form.input-select>

        @if (config('forum.general.soft_deletes'))
            <x-forum::form.input-checkbox
                id="permadelete"
                value=""
                :label="trans('forum::general.perma_delete')"
                x-show="selectedAction == 'delete'"
                x-model="permadelete" />
        @endif

        <x-forum::form.input-select
            id="destination-category"
            :label="trans_choice('forum::categories.category', 1)"
            x-show="selectedAction == 'move'"
            x-model="destinationCategory">
            <option value="0" disabled>...</option>
            @include ('forum::components.category.options', ['categories' => $threadDestinationCategories, 'disable' => $category->id])
        </x-forum::form.input-select>

        <x-forum::button
            :label="trans('forum::general.proceed')"
            @click="applySelectedAction"
            x-bind:disabled="selectedAction == 'none' || selectedAction == 'move' && destinationCategory == 0" />
    </div>

    {{-- Pagination --}}
    {{ $threads->links('forum::components.pagination') }}
</div>

@script
<script>
Alpine.data('category', () => {
    return {
        toggledAllThreads: false,
        selectedThreads: [],
        selectedAction: 'none',
        permadelete: false,
        destinationCategory: 0,
        confirmMessage: "{{ trans('forum::general.generic_confirm') }}",
        reset() {
            this.toggledAllThreads = false;
            this.selectedThreads = [];
            this.permadelete = false;
            this.destinationCategory = 0;
        },
        onThreadChanged(event) {
            if (event.detail.isSelected) {
                this.selectedThreads.push(event.detail.id);
            } else {
                this.selectedThreads.splice(this.selectedThreads.indexOf(event.detail.id), 1);
            }
        },
        onPageChanged(event) {
            this.reset();
        },
        async applySelectedAction() {
            if (this.selectedAction == null || this.selectedThreads.length == 0) {
                return;
            }

            let result;
            switch (this.selectedAction) {
                case 'delete':
                    if (!confirm(this.confirmMessage)) return;
                    result = await $wire.deleteThreads(this.selectedThreads, this.permadelete);
                    break;
                case 'restore':
                    result = await $wire.restoreThreads(this.selectedThreads);
                    break;
                case 'move':
                    result = await $wire.moveThreads(this.selectedThreads, this.destinationCategory);
                    break;
                case 'lock':
                    result = await $wire.lockThreads(this.selectedThreads);
                    break;
                case 'unlock':
                    result = await $wire.unlockThreads(this.selectedThreads);
                    break;
                case 'pin':
                    result = await $wire.pinThreads(this.selectedThreads);
                    break;
                case 'unpin':
                    result = await $wire.unpinThreads(this.selectedThreads);
                    break;
            }

            if (result.type == 'success') this.reset();
            $dispatch('alert', result);
        },
        toggleAllThreads(event) {
            this.toggledAllThreads = !this.toggledAllThreads;
            if (!this.toggledAllThreads) this.selectedThreads = [];
            const checkboxes = document.querySelectorAll('[data-thread] input[type=checkbox]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.toggledAllThreads;
                checkbox.dispatchEvent(new Event('change'));
            });
        }
    }
});
</script>
@endscript
