<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ trans('forum::general.quick_reply') }}</h2>

    <textarea 
        wire:model.defer="content"
        placeholder="Write your reply..."
        class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 placeholder-gray-400 outline-none transition-colors resize-y min-h-32"
    ></textarea>

    <div class="text-right mt-4">
        <button 
            wire:click="reply"
            class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
        >
            {{ trans('forum::general.reply') }}
        </button>
    </div>
</div>