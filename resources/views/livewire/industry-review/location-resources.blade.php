<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Crew Resources - {{ ucfirst($location) }}</h1>
            <p class="text-gray-600 mb-6">All industry resources available in this location</p>

            {{-- Yachts --}}
            @if(count($yachts) > 0)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">🚢 Yachts ({{ count($yachts) }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($yachts as $yacht)
                            <a href="{{ route('yacht-reviews.show', $yacht->slug) }}" class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition">
                                <p class="font-semibold">{{ $yacht->name }}</p>
                                <p class="text-sm text-gray-600">{{ $yacht->rating_avg }}/5 ⭐ ({{ $yacht->reviews_count }} reviews)</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Marinas --}}
            @if(count($marinas) > 0)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">⚓ Marinas ({{ count($marinas) }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($marinas as $marina)
                            <a href="{{ route('marina-reviews.show', $marina->slug) }}" class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition">
                                <p class="font-semibold">{{ $marina->name }}</p>
                                <p class="text-sm text-gray-600">{{ $marina->rating_avg }}/5 ⭐ ({{ $marina->reviews_count }} reviews)</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Contractors --}}
            @if(count($contractors) > 0)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">🔧 Contractors ({{ count($contractors) }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($contractors as $contractor)
                            <a href="{{ route('contractor-reviews.show', $contractor->slug) }}" class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition">
                                <p class="font-semibold">{{ $contractor->name }}</p>
                                <p class="text-sm text-gray-600">{{ $contractor->rating_avg }}/5 ⭐ ({{ $contractor->reviews_count }} reviews)</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Brokers --}}
            @if(count($brokers) > 0)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">🤝 Brokers ({{ count($brokers) }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($brokers as $broker)
                            <a href="{{ route('broker-reviews.show', $broker->slug) }}" class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition">
                                <p class="font-semibold">{{ $broker->name }}</p>
                                <p class="text-sm text-gray-600">{{ $broker->rating_avg }}/5 ⭐ ({{ $broker->reviews_count }} reviews)</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Restaurants --}}
            @if(count($restaurants) > 0)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">🍽️ Restaurants ({{ count($restaurants) }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($restaurants as $restaurant)
                            <a href="{{ route('restaurant-reviews.show', $restaurant->slug) }}" class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition">
                                <p class="font-semibold">{{ $restaurant->name }}</p>
                                <p class="text-sm text-gray-600">{{ $restaurant->rating_avg }}/5 ⭐ ({{ $restaurant->reviews_count }} reviews)</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(count($yachts) === 0 && count($marinas) === 0 && count($contractors) === 0 && count($brokers) === 0 && count($restaurants) === 0)
                <div class="text-center py-12 text-gray-500">
                    <p>No resources found for this location.</p>
                </div>
            @endif
        </div>
    </div>
</div>
