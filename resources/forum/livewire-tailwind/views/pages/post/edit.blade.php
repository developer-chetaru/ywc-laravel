<div x-data="editPost">
    <div class="flex justify-center items-center">
        <div class="grow">
            <h1 class="mb-2">{{ trans('forum::posts.edit') }}</h1>
            <h2 class="mb-4 text-gray-600">Re: {{ $post->thread->title }}</h2>

            <div class="mb-4 text-right">
                <x-forum::link-button
                    :href="$post->route"
                    :label="trans('forum::threads.view')" 
                    class="link-button inline-block rounded-md font-medium text-sm text-center text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors min-w-36 px-4 py-2"/>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 my-4 p-6">
                <form wire:submit="save">
                    <x-forum::form.input-textarea
                        id="content"
                        wire:model="content" />

                    <div class="flex mt-6 gap-4">
                        <div class="grow">
                            <x-forum::button
                                intent="danger"
                                :label="trans('forum::general.delete')"
                                @click.prevent="requestDelete" 
                                class="inline-block rounded-md font-medium text-sm text-center disabled:text-gray-400 disabled:bg-gray-200 disabled:cursor-not-allowed text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors px-6 py-2"/>
                        </div>
                        <div>
                            <x-forum::button :label="trans('forum::general.save')" type="submit" 
                            class="inline-block rounded-md font-medium text-sm text-center disabled:text-gray-400 disabled:bg-gray-200 disabled:cursor-not-allowed text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-6 py-2"/>
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
