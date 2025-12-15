<div>
    <main class="flex-1">
        <div class="w-full h-screen flex flex-wrap">
            <div class="w-full bg-white p-5 rounded-md pb-10">
                <h2 class="text-[#0053FF] text-[30px] font-semibold mb-4 border-b border-[#000000] pb-2">
                    Training & Resources
                </h2>
                
                <!-- Hero Section -->
                <div class="flex w-full flex-col bg-[#0053FF] p-6 py-8 justify-center items-center rounded-lg mb-6" 
                     style="background-image: url(/images/ad-bg-image.png); background-repeat: no-repeat; background-size: cover;">
                    <h3 class="text-[36px] text-[#fff] font-bold mb-2">Advance Your Career at Sea</h3>
                    <p class="text-[20px] text-[#fff]">
                        @if($isYwcMember)
                            As a YWC member, unlock professional courses & certifications at 20% OFF.
                        @else
                            Discover professional courses & certifications. Join YWC for 20% OFF all courses.
                        @endif
                    </p>
                </div>

                <!-- Search and Filters -->
                <div class="w-full mb-6">
                    <div class="grid items-center md:grid-cols-2 gap-[12px] grid-cols-1">
                        <!-- Search Bar -->
                        <div class="flex items-center">
                            <div class="bg-[#f6f8fa] relative max-w-[100%] rounded-lg w-full">
                                <button class="absolute top-3 left-3 flex items-center" type="button">
                                    <img src="/images/search.svg" alt="">
                                </button>
                                <input 
                                    wire:model.live.debounce.300ms="search"
                                    class="w-full bg-transparent placeholder:text-slate-400 !placeholder-[#808080] text-slate-700 text-sm border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 !rounded-lg pl-3 pr-5 py-3 pl-10" 
                                    placeholder="Search certifications (STCW, ENG1, SSO, etc.)">
                            </div>
                        </div>
                        
                        <!-- Filter Dropdowns -->
                        <div class="flex items-center justify-end gap-2 flex-wrap">
                            <div class="relative flex-1 min-w-[150px]">
                                <select wire:model.live="categoryFilter" 
                                        class="appearance-none cursor-pointer w-full px-3 py-3 pr-6 flex bg-[#f6f8fa] items-center rounded-lg text-[14px] text-[#808080] border border-gray-200 focus:outline-none focus:border focus:!border-blue-200">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative flex-1 min-w-[150px]">
                                <select wire:model.live="providerFilter" 
                                        class="appearance-none cursor-pointer w-full px-3 py-3 pr-6 flex bg-[#f6f8fa] items-center rounded-lg text-[14px] text-[#808080] border border-gray-200 focus:outline-none focus:border focus:!border-blue-200">
                                    <option value="">All Providers</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative flex-1 min-w-[120px]">
                                <select wire:model.live="durationFilter" 
                                        class="appearance-none cursor-pointer w-full px-3 pr-6 py-3 flex bg-[#f6f8fa] items-center rounded-lg text-[14px] text-[#808080] border border-gray-200 focus:outline-none focus:border focus:!border-blue-200">
                                    <option value="">Duration</option>
                                    <option value="half-day">Half Day</option>
                                    <option value="1-day">1 Day</option>
                                    <option value="2-3-days">2-3 Days</option>
                                    <option value="4-5-days">4-5 Days</option>
                                    <option value="6-plus">6+ Days</option>
                                </select>
                            </div>
                            <div class="relative flex-1 min-w-[120px]">
                                <select wire:model.live="formatFilter" 
                                        class="appearance-none cursor-pointer w-full px-3 pr-6 py-3 flex bg-[#f6f8fa] items-center rounded-lg text-[14px] text-[#808080] border border-gray-200 focus:outline-none focus:border focus:!border-blue-200">
                                    <option value="">Format</option>
                                    <option value="in-person">In-Person</option>
                                    <option value="online">Online</option>
                                    <option value="hybrid">Hybrid</option>
                                    <option value="self-paced">Self-Paced</option>
                                </select>
                            </div>
                            @if(isset($countries) && $countries->count() > 0)
                            <div class="relative flex-1 min-w-[150px]">
                                <select wire:model.live="locationFilter" 
                                        class="appearance-none cursor-pointer w-full px-3 pr-6 py-3 flex bg-[#f6f8fa] items-center rounded-lg text-[14px] text-[#808080] border border-gray-200 focus:outline-none focus:border focus:!border-blue-200">
                                    <option value="">Location</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country }}">{{ $country }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="relative flex-1 min-w-[150px]">
                                <select wire:model.live="sortBy" 
                                        class="appearance-none cursor-pointer w-full px-3 pr-6 py-3 flex bg-[#f6f8fa] items-center rounded-lg text-[14px] text-[#808080] border border-gray-200 focus:outline-none focus:border focus:!border-blue-200">
                                    <option value="relevance">Sort By</option>
                                    <option value="price_low">Price: Low to High</option>
                                    <option value="price_high">Price: High to Low</option>
                                    <option value="rating">Highest Rated</option>
                                    <option value="popular">Most Popular</option>
                                    <option value="newest">Newest First</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    @if($categoryFilter || $providerFilter || $durationFilter || $locationFilter || $formatFilter)
                        <div class="mt-4 flex items-center gap-2">
                            <button wire:click="clearFilters" 
                                    class="px-4 py-2 text-sm text-[#0053FF] border border-[#0053FF] rounded-lg hover:bg-[#0053FF] hover:text-white">
                                Clear Filters
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Course Grid -->
                <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($courses as $course)
                        <div class="flex flex-wrap overflow-hidden bg-[#F5F6FA] rounded-lg flex-col relative hover:shadow-lg transition-shadow">
                            <!-- Course Image -->
                            <div class="w-full images shrink-0 h-48 bg-gray-200">
                                @if($course->certification->cover_image)
                                    @php
                                        $imagePath = $course->certification->cover_image;
                                        // If path starts with 'images/', use asset() directly, otherwise use storage
                                        $imageUrl = str_starts_with($imagePath, 'images/') 
                                            ? asset($imagePath) 
                                            : asset('storage/' . $imagePath);
                                    @endphp
                                    <img src="{{ $imageUrl }}" 
                                         alt="{{ $course->certification->name }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2L3 7v11h14V7l-7-5z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Course Content -->
                            <div class="text-content flex flex-col p-4 flex-1">
                                <span class="text-[#616161] text-[14px] pb-2 font-[500]">
                                    {{ $course->provider->name }}
                                </span>
                                <h4 class="text-[#0053FF] text-[18px] mb-2 font-[600] line-clamp-2">
                                    {{ $course->certification->name }}
                                </h4>
                                
                                @if($course->certification->official_designation)
                                    <p class="text-[#616161] text-[14px] pb-2 font-[400]">
                                        {{ $course->certification->official_designation }}
                                    </p>
                                @endif
                                
                                <!-- Rating -->
                                @if($course->rating_avg > 0)
                                    <div class="flex items-center gap-1 mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= round($course->rating_avg) ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span class="text-sm text-gray-600">({{ $course->review_count }})</span>
                                    </div>
                                @endif
                                
                                <!-- Price -->
                                <div class="w-full mt-auto">
                                    <div class="mb-2">
                                        @if($isYwcMember)
                                            <span class="price text-[18px] text-[#232323] font-bold">
                                                £{{ number_format($course->ywc_price, 2) }}
                                            </span>
                                            <span class="text-[#808080] text-[14px] line-through ml-2">
                                                £{{ number_format($course->price, 2) }}
                                            </span>
                                            <span class="text-green-600 text-[12px] ml-2">
                                                Save £{{ number_format($course->savings_amount, 2) }}
                                            </span>
                                        @else
                                            <span class="price text-[18px] text-[#232323] font-bold">
                                                £{{ number_format($course->price, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Course Details -->
                                    <div class="text-xs text-gray-600 mb-2 space-y-1">
                                        <div>Duration: {{ $course->duration_days }} day(s)</div>
                                        <div>Format: {{ ucfirst(str_replace('-', ' ', $course->format)) }}</div>
                                        @if($course->upcomingSchedules->count() > 0)
                                            <div>Next: {{ $course->upcomingSchedules->first()->start_date->format('M d, Y') }}</div>
                                        @endif
                                    </div>
                                    
                                    <a href="{{ route('training.certification.detail', $course->certification->slug) }}" 
                                       class="mt-2 w-full px-5 py-2.5 flex bg-[#fff] items-center border-[#0053FF] border rounded-lg text-[14px] text-[#0053FF] center font-[600] justify-center hover:bg-[#0053FF] hover:text-[#fff] transition-colors">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500 text-lg">No courses found matching your criteria.</p>
                            <button wire:click="clearFilters" 
                                    class="mt-4 px-4 py-2 text-[#0053FF] border border-[#0053FF] rounded-lg hover:bg-[#0053FF] hover:text-white">
                                Clear Filters
                            </button>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </main>
</div>
