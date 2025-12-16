<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">My Preferred Crew</h1>

            <div class="space-y-4">
                @forelse($preferredCrew as $pref)
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold mb-2">{{ $pref->crew->name }}</h3>
                            <p class="text-sm text-gray-600">
                                Worked together: {{ $pref->times_worked_together }} times
                                @if($pref->last_worked_at)
                                • Last worked: {{ $pref->last_worked_at->diffForHumans() }}
                                @endif
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="removeFromList({{ $pref->id }})" class="px-4 py-2 text-red-600 border border-red-300 rounded-md">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white shadow rounded-lg p-12 text-center">
                    <p class="text-gray-500">No preferred crew yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
