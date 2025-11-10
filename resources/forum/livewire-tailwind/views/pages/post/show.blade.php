<div>
    <div class="flex justify-center items-center">
        <div class="grow max-w-screen-xl">
            <h1 class="mb-2">{{ trans('forum::posts.view') }}</h1>
            <h2 class="text-slate-500">Re: {{ $post->thread->title }}</h2>

            <div class="text-right">
                <x-forum::link-button :href="$post->route" :label="trans('forum::threads.view')" 
                class="inline-block rounded-md font-medium text-l text-center disabled:text-slate-500 disabled:bg-slate-300 dark:disabled:text-slate-300 dark:disabled:bg-slate-500 text-white bg-blue-600 hover:text-white hover:bg-blue-500 px-6 py-2"/>
            </div>

            <livewire:forum::components.post.card :$post :single="true" />
        </div>
    </div>
</div>
