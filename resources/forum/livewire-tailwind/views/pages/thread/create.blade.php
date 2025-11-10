<div>
    <div class="flex justify-center items-center">
        <div class="grow">
            <h1>{{ trans('forum::threads.new_thread') }} ({{ $category->title }})</h1>

            <div class="bg-white rounded-md shadow-md my-2 p-6 dark:bg-slate-700">
                <form wire:submit="create">
                    <x-forum::form.input-text
                        id="title"
                        value=""
                        :label="trans('forum::general.title')"
                        wire:model="title" />

                    <x-forum::form.input-textarea
                        id="content"
                        wire:model="content" />

                    <div class="flex mt-6">
                        <div class="grow">
                            <x-forum::button
                                href="{{ URL::previous() }}"
                                intent="secondary"
                                label="{{ trans('forum::general.cancel') }}" class="inline-block rounded-md font-medium text-l text-center disabled:text-slate-500 disabled:bg-slate-300 dark:disabled:text-slate-300 dark:disabled:bg-slate-500 text-zinc-800 bg-zinc-400/50 hover:bg-zinc-400/35 dark:text-slate-800 dark:bg-slate-400/50 dark:hover:bg-slate-400/65 px-4 py-2" />
                        </div>
                        <div class="grow text-right">
                            <x-forum::button :label="trans('forum::general.create')" type="submit" class="rounded-md font-medium text-l text-center text-white bg-blue-600 hover:text-white hover:bg-blue-500 px-4 py-2" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
