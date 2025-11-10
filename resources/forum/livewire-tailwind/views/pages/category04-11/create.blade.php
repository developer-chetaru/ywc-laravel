<div class="w-full">
    <div class="flex justify-center items-start w-full">
        <div class="w-full relative px-4 md:px-8">
            <div class="flex items-center justify-between mb-4">
                {{-- Page Heading --}}
                <h2 class="text-3xl text-[#0053FF] font-semibold">{{ trans('forum::categories.create') }}</h2>

                {{-- Back Button --}}
                <x-forum::link-button
                    :href="route('forum.category.index')"
                    :label="'← Back'"
                    class="link-button inline-block rounded-md font-medium text-l text-center text-white bg-blue-600 hover:text-white hover:bg-blue-500 px-4 py-2" />
            </div>

            {{-- Form Container --}}
            <div class="bg-white rounded-md shadow-md my-2 p-6 dark:bg-slate-700 w-full">
                <form wire:submit="create">
                    <x-forum::form.input-text
                        id="title"
                        :label="trans('forum::general.title')"
                        wire:model="title" />

                    <x-forum::form.input-text
                        id="description"
                        :label="trans('forum::general.description')"
                        wire:model="description" />

                    @if ($categories->count() > 0)
                        <x-forum::form.input-select
                            id="parent-category"
                            :label="trans('forum::categories.parent')"
                            wire:model="parent_category">
                            <option value="0">{{ trans('forum::general.none') }}</option>
                            @include('forum::components.category.options', ['categories' => $categories])
                        </x-forum::form.input-select>
                    @endif

                    <x-forum::form.input-checkbox
                        id="accepts-threads"
                        :label="trans('forum::categories.enable_threads')"
                        wire:model="accepts_threads" />

                    {{-- Submit Button --}}
                    <div class="mt-4">
                        <x-forum::button :label="trans('forum::general.create')" type="submit"  class="inline-block rounded-md font-medium text-l text-center disabled:text-slate-500 disabled:bg-slate-300 dark:disabled:text-slate-300 dark:disabled:bg-slate-500 text-white bg-blue-600 hover:text-white hover:bg-blue-500  px-4 py-2"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
