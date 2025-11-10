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
                            $dashboardItems = [
                                ['label' => 'Career History', 'defaultImage' => '/images/manage-document-black.svg', 'hoverImage' => '/images/manage-dashbaord.svg', 'url' => '/career-history'],
                                ['label' => 'Legal Support', 'defaultImage' => '/images/justice-scale-01.svg', 'hoverImage' => '/images/justice-scale-white.svg', 'url' => route('legal-support.index')],
                                ['label' => 'Mentor Health Support', 'defaultImage' => '/images/brain-02.svg', 'hoverImage' => '/images/brain-white.svg', 'url' => '/documents', 'disabled' => true],
                                ['label' => 'Department Forums', 'defaultImage' => '/images/message-multiple-01 (1).svg', 'hoverImage' => '/images/message-multiple-white.svg', 'url' => '/forum'],
                                ['label' => 'Financial Future Planning', 'defaultImage' => '/images/save-money-dollar.svg', 'hoverImage' => '/images/save-money-dollar-white.svg', 'url' => '/training', 'disabled' => true],
                                ['label' => 'Pension & Investment Advice', 'defaultImage' => '/images/money-bag-ywc.svg', 'hoverImage' => '/images/money-bag-white.svg', 'url' => '/weather', 'disabled' => true],
                                ['label' => 'Industry Review System', 'defaultImage' => '/images/Review (2).svg', 'hoverImage' => '/images/Review-white.svg', 'url' => '/review', 'disabled' => true],
                                ['label' => 'Itinerary System', 'defaultImage' => '/images/itinerarySystem.svg', 'hoverImage' => '/images/itinerarySystemWhite.svg', 'url' => '/itinerary-system', 'disabled' => true],
                            ];
                        @endphp

                        @foreach ($dashboardItems as $item)
                            @php
                                $isDisabled = isset($item['disabled']) && $item['disabled'];
                                $isActive = request()->is(ltrim($item['url'], '/'));
                            @endphp

                            @if($isDisabled)
                                {{-- Disabled card --}}
                                <div class="flex flex-col items-center justify-center p-6 bg-gray-200 rounded-lg shadow-sm min-h-[140px] opacity-50 cursor-not-allowed">
                                    <img src="{{ $item['defaultImage'] }}" alt="{{ $item['label'] }}" class="w-8 h-8">
                                    <div class="mt-3 text-center font-medium text-base text-gray-500">{{ $item['label'] }}</div>
                                </div>
                            @else
                                {{-- Active / clickable card --}}
                                <a href="{{ $item['url'] }}"
                                class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg shadow-sm min-h-[140px] transition cursor-pointer group hover:bg-blue-500 hover:shadow-md">
                                
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
                            @endif
                        @endforeach
                    </div>
                    
                @endrole
            </div>
        </div>
    </div>
</x-app-layout>
