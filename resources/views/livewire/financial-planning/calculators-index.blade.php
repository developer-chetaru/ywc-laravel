<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <div class="flex justify-between items-start mb-6">
                <div class="flex-1">
                    @guest
                        <a href="{{ route('landing') }}" 
                           class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-700 mb-4 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            <span class="font-medium">Back to Home</span>
                        </a>
                    @endguest
                    <h1 class="text-4xl font-bold text-gray-900 mb-3">Financial Calculators</h1>
                    <p class="text-gray-600">Free financial planning calculators to help you make informed decisions. No account required!</p>
                </div>
                @auth
                    <a href="{{ route('financial.dashboard') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        View Dashboard
                    </a>
                @endauth
            </div>

            @auth
                @php
                    $user = auth()->user();
                    $hasActiveSubscription = $user->hasActiveSubscription();
                    $subscription = $user->activeSubscription();
                @endphp

                @if(!$hasActiveSubscription)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-yellow-900">✨ Upgrade to Premium</h3>
                                <p class="text-sm text-yellow-800 mt-1">Save unlimited calculations, access advanced features, and get personalized financial advice.</p>
                            </div>
                            <a href="{{ route('subscription.page') }}" 
                               class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors whitespace-nowrap">
                                View Plans
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-8">
                        <div class="flex items-center">
                            <span class="text-green-600 font-semibold">✅ Premium Member - Full Access</span>
                            @if($subscription)
                                @php
                                    $planName = 'Premium';
                                    if ($subscription->interval) {
                                        $planName = ucfirst($subscription->interval) . ($subscription->interval === 'year' ? ' Plan' : 'ly Plan');
                                    }
                                @endphp
                                <span class="ml-2 text-sm text-green-700">
                                    ({{ $planName }})
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            @endauth

            @foreach($categories as $categoryName => $calculators)
                <div class="mb-10 last:mb-0">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4 pb-2 border-b-2 border-gray-200">{{ $categoryName }}</h2>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($calculators as $calculator)
                            <a href="{{ isset($calculator['coming_soon']) && $calculator['coming_soon'] ? '#' : route($calculator['route']) }}"
                               class="block p-6 bg-gray-50 hover:bg-blue-50 rounded-lg border border-gray-200 hover:border-blue-300 transition-all {{ isset($calculator['coming_soon']) && $calculator['coming_soon'] ? 'opacity-75 cursor-not-allowed' : 'cursor-pointer' }}">
                                <div class="flex items-start gap-4">
                                    <span class="text-3xl">{{ $calculator['icon'] }}</span>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 mb-1">{{ $calculator['name'] }}</h3>
                                        <p class="text-sm text-gray-600">{{ $calculator['description'] }}</p>
                                        @if(isset($calculator['coming_soon']) && $calculator['coming_soon'])
                                            <span class="inline-block mt-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Coming Soon</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Call to Action for Account Creation --}}
            @guest
                <div class="mt-8 p-6 bg-blue-50 border border-blue-200 rounded-lg">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Create a Free Account</h3>
                    <p class="text-blue-800 mb-4">Save your calculations, track your progress, and access premium features like goal tracking, expense management, and personalized retirement planning.</p>
                    <a href="{{ route('register') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Sign Up Free
                    </a>
                </div>
            @endguest
        </div>
    </div>
</div>
