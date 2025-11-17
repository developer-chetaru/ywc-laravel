<div>
    <div class="flex justify-center items-center">
        <div class="grow max-w-screen-xl">
            <h1 class="mb-2">{{ trans('forum::posts.view') }}</h1>
            <h2 class="text-slate-500">Re: {{ $post->thread->title }}</h2>

            <div class="text-right">
                <x-forum::link-button :href="$post->route" :label="trans('forum::threads.view')" 
                class="inline-block rounded-md font-medium text-l text-center disabled:text-gray-400 disabled:bg-gray-200 disabled:cursor-not-allowed text-white bg-blue-600 hover:text-white hover:bg-blue-700 px-6 py-2 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"/>
            </div>

            <livewire:forum::components.post.card :$post :single="true" />
        </div>
    </div>
</div>
