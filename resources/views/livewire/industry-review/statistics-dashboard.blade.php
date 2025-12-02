<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Industry Review System - Statistics Dashboard</h1>

            {{-- Overview Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                    <h3 class="text-lg font-semibold mb-2">Total Reviews</h3>
                    <p class="text-4xl font-bold">{{ number_format($totalReviews) }}</p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white">
                    <h3 class="text-lg font-semibold mb-2">Total Entities</h3>
                    <p class="text-4xl font-bold">{{ number_format($stats['yachts']['total'] + $stats['marinas']['total'] + $stats['contractors']['total'] + $stats['brokers']['total'] + $stats['restaurants']['total']) }}</p>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-6 text-white">
                    <h3 class="text-lg font-semibold mb-2">Flagged Content</h3>
                    <p class="text-4xl font-bold">{{ number_format($stats['moderation']['flagged']) }}</p>
                </div>
            </div>

            {{-- Detailed Statistics --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Yacht Reviews --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">🚢 Yacht Reviews</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Yachts in Database:</span>
                            <span class="font-semibold">{{ number_format($stats['yachts']['total']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Reviews:</span>
                            <span class="font-semibold">{{ number_format($stats['yachts']['reviews']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Average Rating:</span>
                            <span class="font-semibold">{{ $stats['yachts']['avg_rating'] }}/5 ⭐</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Recommendation Rate:</span>
                            <span class="font-semibold">{{ $stats['yachts']['recommend_rate'] }}%</span>
                        </div>
                    </div>
                </div>

                {{-- Marina Reviews --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">⚓ Marina Reviews</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Marinas in Database:</span>
                            <span class="font-semibold">{{ number_format($stats['marinas']['total']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Reviews:</span>
                            <span class="font-semibold">{{ number_format($stats['marinas']['reviews']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Average Rating:</span>
                            <span class="font-semibold">{{ $stats['marinas']['avg_rating'] }}/5 ⭐</span>
                        </div>
                    </div>
                </div>

                {{-- Contractor Reviews --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">🔧 Contractor Reviews</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Contractors in Database:</span>
                            <span class="font-semibold">{{ number_format($stats['contractors']['total']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Reviews:</span>
                            <span class="font-semibold">{{ number_format($stats['contractors']['reviews']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Average Rating:</span>
                            <span class="font-semibold">{{ $stats['contractors']['avg_rating'] }}/5 ⭐</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Recommendation Rate:</span>
                            <span class="font-semibold">{{ $stats['contractors']['recommend_rate'] }}%</span>
                        </div>
                    </div>
                </div>

                {{-- Broker Reviews --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">🤝 Broker Reviews</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Brokers in Database:</span>
                            <span class="font-semibold">{{ number_format($stats['brokers']['total']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Reviews:</span>
                            <span class="font-semibold">{{ number_format($stats['brokers']['reviews']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Average Rating:</span>
                            <span class="font-semibold">{{ $stats['brokers']['avg_rating'] }}/5 ⭐</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Recommendation Rate:</span>
                            <span class="font-semibold">{{ $stats['brokers']['recommend_rate'] }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Would Use Again:</span>
                            <span class="font-semibold">{{ $stats['brokers']['use_again_rate'] }}%</span>
                        </div>
                    </div>
                </div>

                {{-- Restaurant Reviews --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">🍽️ Restaurant Reviews</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Restaurants in Database:</span>
                            <span class="font-semibold">{{ number_format($stats['restaurants']['total']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Reviews:</span>
                            <span class="font-semibold">{{ number_format($stats['restaurants']['reviews']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Average Rating:</span>
                            <span class="font-semibold">{{ $stats['restaurants']['avg_rating'] }}/5 ⭐</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Recommendation Rate:</span>
                            <span class="font-semibold">{{ $stats['restaurants']['recommend_rate'] }}%</span>
                        </div>
                    </div>
                </div>

                {{-- Moderation Stats --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">🛡️ Moderation</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Flagged Reviews:</span>
                            <span class="font-semibold text-red-600">{{ number_format($stats['moderation']['flagged']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pending Approval:</span>
                            <span class="font-semibold text-yellow-600">{{ number_format($stats['moderation']['pending']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
