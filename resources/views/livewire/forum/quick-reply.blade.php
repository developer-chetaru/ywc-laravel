<div class="bg-white rounded-md shadow-md p-6 mt-4 dark:bg-slate-700">
    <h2 class="text-lg font-semibold mb-4">{{ trans('forum::general.quick_reply') }}</h2>

    <textarea 
        wire:model.defer="content"
        placeholder="Write your reply..."
        class="w-full border rounded-md p-2"
    ></textarea>

    <div class="text-right mt-4">
        <button 
            wire:click="reply"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
        >
            {{ trans('forum::general.reply') }}
        </button>
    </div>
</div>