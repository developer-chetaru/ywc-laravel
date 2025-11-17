<div x-data="editCategory" class="w-full max-w-4xl mx-auto px-4 py-6">
    @if(!isset($category) || is_null($category))
        <div class="p-4 bg-red-100 border border-red-300 rounded-lg">
            <p class="text-red-800">Error: Category not found. Please go back and select a valid category to edit.</p>
            <a href="{{ route('forum.category.index') }}" class="text-blue-600 hover:underline">Return to Forums</a>
        </div>
    @else
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800 mb-2">{{ trans('forum::categories.edit') }}</h1>
        <p class="text-sm text-gray-600">Editing: <span class="font-medium text-gray-800">{{ $category->title }}</span></p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 md:p-8">
        <form wire:submit="save">
            <div class="mb-6">
                <x-forum::form.input-text
                    id="title"
                    :label="trans('forum::general.title')"
                    wire:model="title" />
            </div>

            <div class="mb-6">
                <x-forum::form.input-text
                    id="description"
                    :label="trans('forum::general.description')"
                    wire:model="description" />
            </div>

            @if ($categories->count() > 0)
            <div class="mb-6">
                <x-forum::form.input-select
                    id="parent-category"
                    :label="trans('forum::categories.parent')"
                    wire:model="parent_category">
                    <option value="0">{{ trans('forum::general.none') }}</option>
                    @include ('forum::components.category.options', ['categories' => $categories])
                </x-forum::form.input-select>
            </div>
            @endif

            <div class="mb-6 space-y-4">
                <x-forum::form.input-checkbox
                    id="accepts-threads"
                    :label="trans('forum::categories.enable_threads')"
                    wire:model="accepts_threads" />

                <x-forum::form.input-checkbox
                    id="make-private"
                    :label="trans('forum::categories.make_private')"
                    wire:model="is_private" />
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <button type="button"
                        @click.prevent="requestDelete"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    {{ trans('forum::general.delete') }}
                </button>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    {{ trans('forum::general.save') }}
                </button>
            </div>
        </form>
    </div>
    @endif

    <x-forum::modal
        :heading="trans('forum::general.generic_confirm')"
        x-show="showDeleteModal"
        onClose="showDeleteModal = false">
        {{ trans('forum::categories.confirm_nonempty_delete') }}

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
Alpine.data('editCategory', () => {
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
