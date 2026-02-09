<x-app-layout>
    <div>
        <div class="max-w-8xl mx-auto sm:px-4 lg:px-4">
            <div class="bg-white p-10 rounded-lg shadow-md">
                @role('super_admin')
					<div class="text-center text-gray-600">
                        Admin dashboard coming soon
                    </div>
                @else
					<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @php
                            // Dashboard cards: defaultImage shows on gray bg (needs colored icon), hoverImage shows on blue bg (needs white icon)
                            $dashboardItems = [
                                ['label' => 'Career History', 'defaultImage' => '/images/career.svg', 'hoverImage' => '/images/manage-dashbaord.svg', 'url' => auth()->check() ? route('career-history', auth()->id()) : route('login')],
                                ['label' => 'Documents', 'defaultImage' => '/images/document-black.svg', 'hoverImage' => '/images/document.svg', 'url' => route('documents')],
                                ['label' => 'Analytics', 'defaultImage' => '/images/dashboard.svg', 'hoverImage' => '/images/dashboard-black.svg', 'url' => route('analytics.user-dashboard')],
                                ['label' => 'Legal Support', 'defaultImage' => '/images/justice-scale-01.svg', 'hoverImage' => '/images/justice-scale-white.svg', 'url' => route('legal-support.index')],
                                ['label' => 'Training & Resources', 'defaultImage' => '/images/training-and-resources-active.svg', 'hoverImage' => '/images/training-and-resources-default.svg', 'url' => route('training.resources')],
                                ['label' => 'Mental Health Support', 'defaultImage' => '/images/brain-02.svg', 'hoverImage' => '/images/brain-white.svg', 'url' => route('mental-health.dashboard')],
                                ['label' => 'Department Forums', 'defaultImage' => '/images/message-multiple-01 (1).svg', 'hoverImage' => '/images/message-multiple-white.svg', 'url' => '/forum'],
                                ['label' => 'Financial Future Planning', 'defaultImage' => '/images/save-money-dollar.svg', 'hoverImage' => '/images/save-money-dollar-white.svg', 'url' => auth()->check() ? route('financial.dashboard') : route('financial.calculators.index')],
                                ['label' => 'Industry Review System', 'defaultImage' => '/images/industry-review-active.svg', 'hoverImage' => '/images/industry-review-default.svg', 'url' => route('industryreview.index')],
                                ['label' => 'Itinerary System', 'defaultImage' => '/images/itinerarySystem.svg', 'hoverImage' => '/images/itinerarySystemWhite.svg', 'url' => route('itinerary.routes.index')],
                                ['label' => 'Market Place', 'defaultImage' => '/images/market-place-active-icon.svg', 'hoverImage' => '/images/market-place-default-icon.svg', 'url' => route('marketplace.index')],
                                ['label' => 'Work Log', 'defaultImage' => '/images/work-log-active.svg', 'hoverImage' => '/images/work-log-defult.svg', 'url' => route('worklog.index')],
                                ['label' => 'Work Schedules', 'defaultImage' => '/images/calendar-03.svg', 'hoverImage' => '/images/calendar-03.svg', 'url' => route('work-schedules.index')],
                                ['label' => 'Job Board', 'defaultImage' => '/images/document.svg', 'hoverImage' => '/images/document-black.svg', 'url' => route('job-board.index')],
                                ['label' => 'Crew Discovery', 'defaultImage' => '/images/discover-circle.svg', 'hoverImage' => '/images/discover-circle-white.svg', 'url' => route('crew.discovery')],
                            ];
                            
                            // Add Captain Dashboard for captains and super_admin
                            if (auth()->check() && (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('captain'))) {
                                $dashboardItems[] = ['label' => 'Captain Dashboard', 'defaultImage' => '/images/captain.svg', 'hoverImage' => '/images/captain.svg', 'url' => route('captain-dashboard.index')];
                            }
                        @endphp

                        @foreach ($dashboardItems as $item)
                            @php
                                $isDisabled = isset($item['disabled']) && $item['disabled'];
                                // Check if current URL matches the item URL
                                $itemUrl = parse_url($item['url'], PHP_URL_PATH);
                                $currentPath = request()->path();
                                $isActive = $itemUrl === '/' . $currentPath || 
                                           request()->is(ltrim($itemUrl, '/')) ||
                                           request()->is(ltrim($itemUrl, '/') . '*');
                            @endphp

                            @if($isDisabled)
                                {{-- Disabled card --}}
                                <div class="flex flex-col items-center justify-center p-6 bg-gray-200 rounded-lg shadow-sm min-h-[140px] opacity-50 cursor-not-allowed">
                                    <img src="{{ $item['defaultImage'] }}" alt="{{ $item['label'] }}" class="w-8 h-8">
                                    <div class="mt-3 text-center font-medium text-base text-gray-500">{{ $item['label'] }}</div>
                                </div>
                            @else
                                {{-- Active / clickable card --}}
                                <div class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg shadow-sm min-h-[140px] transition cursor-pointer group hover:bg-blue-500 hover:shadow-md relative">
                                    <a href="{{ $item['url'] }}" class="flex flex-col items-center justify-center w-full h-full">
                                        {{-- Default icon --}}
                                        <img src="{{ $item['defaultImage'] }}" 
                                            alt="{{ $item['label'] }}" 
                                            class="w-8 h-8 group-hover:hidden {{ $isActive ? 'hidden' : '' }}">
                                        
                                        {{-- Hover / Active icon --}}
                                        <img src="{{ $item['hoverImage'] }}" 
                                            alt="{{ $item['label'] }}" 
                                            class="w-8 h-8 hidden group-hover:block {{ $isActive ? 'block' : '' }}">
                                        
                                        {{-- Text --}}
                                        <div class="mt-3 text-center font-medium text-base
                                            {{ $isActive ? 'text-white' : '' }}
                                            group-hover:text-white">
                                            {{ $item['label'] }}
                                        </div>
                                    </a>
                                    
                                    {{-- Button for guests (not logged in) --}}
                                    @guest
                                        @if($item['label'] === 'Financial Future Planning')
                                            <div class="mt-3 w-full">
                                                <a href="{{ route('financial.calculators.index') }}" 
                                                   onclick="event.stopPropagation();"
                                                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg text-sm font-medium transition-colors shadow-md hover:shadow-lg">
                                                    🧮 Get Started
                                                </a>
                                            </div>
                                        @elseif(!in_array($item['label'], ['Department Forums']))
                                            <div class="mt-3 w-full">
                                                <a href="{{ route('register') }}" 
                                                   onclick="event.stopPropagation();"
                                                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg text-sm font-medium transition-colors shadow-md hover:shadow-lg">
                                                    Sign Up Free
                                                </a>
                                            </div>
                                        @endif
                                    @endguest
                                </div>
                            @endif
                        @endforeach
                    </div>
                    
                @endrole
            </div>
        </div>
    </div>
</x-app-layout>
