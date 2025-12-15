<div>
    <main class="flex-1">
        <div class="w-full bg-white p-5 rounded-md pb-10">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-[#0053FF] text-[30px] font-semibold">Course Schedule Calendar</h2>
                <div class="flex gap-2">
                    <button wire:click="previousMonth" 
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        ← Previous
                    </button>
                    <button wire:click="goToToday" 
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Today
                    </button>
                    <button wire:click="nextMonth" 
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Next →
                    </button>
                </div>
            </div>

            <div class="mb-4 text-center">
                <h3 class="text-2xl font-bold">{{ $currentDate->format('F Y') }}</h3>
            </div>

            <!-- Calendar Grid -->
            <div class="border rounded-lg overflow-hidden">
                <!-- Day Headers -->
                <div class="grid grid-cols-7 bg-gray-100">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="p-2 text-center font-semibold text-gray-700 border-r last:border-r-0">
                            {{ $day }}
                        </div>
                    @endforeach
                </div>

                <!-- Calendar Days -->
                <div class="grid grid-cols-7">
                    @foreach($calendarDays as $day)
                        <div class="min-h-[100px] border-r border-b p-2 
                            {{ !$day['isCurrentMonth'] ? 'bg-gray-50' : '' }}
                            {{ $day['isToday'] ? 'bg-blue-50 border-blue-300' : '' }}">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-sm font-semibold 
                                    {{ $day['isToday'] ? 'text-blue-600' : ($day['isCurrentMonth'] ? 'text-gray-900' : 'text-gray-400') }}">
                                    {{ $day['date']->format('j') }}
                                </span>
                            </div>
                            
                            <!-- Schedules for this day -->
                            <div class="space-y-1">
                                @foreach($day['schedules']->take(2) as $schedule)
                                    <div class="text-xs p-1 rounded bg-[#0053FF] text-white cursor-pointer hover:bg-blue-700"
                                         title="{{ $schedule->providerCourse->certification->name }} - {{ $schedule->providerCourse->provider->name }}">
                                        <div class="truncate">{{ $schedule->providerCourse->certification->name }}</div>
                                        <div class="text-xs opacity-90">
                                            {{ $schedule->start_date->format('H:i') }}
                                            @if($schedule->available_spots)
                                                - {{ $schedule->available_spots - $schedule->booked_spots }} spots
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                @if($day['schedules']->count() > 2)
                                    <div class="text-xs text-gray-600">
                                        +{{ $day['schedules']->count() - 2 }} more
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-4 flex gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-[#0053FF] rounded"></div>
                    <span>Course Available</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-blue-50 border border-blue-300 rounded"></div>
                    <span>Today</span>
                </div>
            </div>
        </div>
    </main>
</div>
