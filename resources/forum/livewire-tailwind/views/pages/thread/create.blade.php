<div>
    @if(!isset($category) || is_null($category))
        <div class="p-4 bg-red-100 border border-red-300 rounded-lg">
            <p class="text-red-800">Error: Category not found. Please go back and select a valid category.</p>
            <a href="{{ route('forum.category.index') }}" class="text-blue-600 hover:underline">Return to Forums</a>
        </div>
    @else
    <div class="w-full max-w-4xl mx-auto px-4 py-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-2">{{ trans('forum::threads.new_thread') }}</h1>
            <p class="text-sm text-gray-600">Category: <span class="font-medium text-gray-800">{{ $category->title ?? 'Unknown Category' }}</span></p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 md:p-8">
            <form wire:submit="create">
                <div class="mb-6">
                    <x-forum::form.input-text
                        id="title"
                        value=""
                        :label="trans('forum::general.title')"
                        wire:model="title" />
                </div>

                <div class="mb-6">
                    <x-forum::form.input-textarea
                        id="content"
                        wire:model="content" />
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ URL::previous() }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        {{ trans('forum::general.cancel') }}
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        {{ trans('forum::general.create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
