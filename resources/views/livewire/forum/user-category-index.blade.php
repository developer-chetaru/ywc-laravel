<div>
    @foreach ($categories as $category)
        <livewire:forum::components.category.card :$category :key="$category->id" />
    @endforeach
</div>
