<div x-data="editPost">
    <div class="flex justify-center items-center">
        <div class="grow">
            <h1 class="mb-2">{{ trans('forum::posts.edit') }}</h1>
            <h2 class="mb-4 text-slate-500">Re: {{ $post->thread->title }}</h2>

            <div class="mb-4 text-right">
                <x-forum::link-button
                    :href="$post->route"
                    :label="trans('forum::threads.view')" 
                    class="link-button inline-block rounded-md font-medium text-l text-center text-white bg-blue-600 hover:text-white hover:bg-blue-500 min-w-36 px-4 py-2"/>
            </div>

            <div class="bg-white rounded-md shadow-md my-2 p-6 dark:bg-slate-700">
                <form wire:submit="save">
                    <x-forum::form.input-textarea
                        id="content"
                        wire:model="content" />

                    <div class="flex mt-6">
                        <div class="grow">
                            <x-forum::button
                                intent="danger"
                                :label="trans('forum::general.delete')"
                                @click.prevent="requestDelete" 
                                class="inline-block rounded-md font-medium text-l text-center disabled:text-slate-500 disabled:bg-slate-300 dark:disabled:text-slate-300 dark:disabled:bg-slate-500 text-white bg-red-500 hover:bg-red-400 px-6 py-2"/>
                        </div>
                        <div>
                            <x-forum::button :label="trans('forum::general.save')" type="submit" 
                            class="inline-block rounded-md font-medium text-l text-center disabled:text-slate-500 disabled:bg-slate-300 dark:disabled:text-slate-300 dark:disabled:bg-slate-500 text-white bg-blue-600 hover:text-white hover:bg-blue-500 px-6 py-2"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-forum::modal
        :heading="trans('forum::general.generic_confirm')"
        x-show="showDeleteModal"
        onClose="showDeleteModal = false">
        {{ trans_choice('forum::posts.confirm_delete', 1) }}

        <div class="flex flex-wrap mt-6">
            <div class="grow">
                <x-forum::button
                    intent="secondary"
                    :label="trans('forum::general.cancel')"
                    @click="showDeleteModal = false" />
            </div>
            <div>
                <x-forum::button
                    intent="primary"
                    :label="trans('forum::general.proceed')"
                    @click="confirmDelete" />
            </div>
        </div>
    </x-forum::modal>
</div>

@script
<script>
Alpine.data('editPost', () => {
    return {
        showDeleteModal: false,
        requestDelete(event) {
            this.showDeleteModal = true;
        },
        confirmDelete() {
            $wire.delete();
        }
    }
});
</script>
@endscript
