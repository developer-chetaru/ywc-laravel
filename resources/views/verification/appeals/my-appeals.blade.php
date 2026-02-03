<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight p-[30px] pl-[60px] pr-[20px] max-[767px]:p-[15px] max-[767px]:pl-[25px]">
            My Appeals
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-gray-600 text-center">Track the status of your verification appeals</p>
                    </div>

                    @forelse($appeals as $appeal)
                    <div class="border rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-lg">{{ $appeal->appeal_reference }}</h3>
                                <p class="text-sm text-gray-600 mt-1">Document: {{ $appeal->document->document_name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600">Filed: {{ $appeal->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 rounded-full text-sm bg-{{ $appeal->status_color }}-100 text-{{ $appeal->status_color }}-800">
                                    {{ ucfirst($appeal->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <p class="text-sm text-gray-700"><strong>Reason:</strong> {{ Str::limit($appeal->reason, 150) }}</p>
                        </div>

                        @if($appeal->resolution)
                        <div class="mt-3 p-3 bg-gray-50 rounded">
                            <p class="text-sm text-gray-700"><strong>Resolution:</strong> {{ $appeal->resolution }}</p>
                            @if($appeal->reviewed_at)
                            <p class="text-xs text-gray-500 mt-1">Reviewed on {{ $appeal->reviewed_at->format('d M Y') }}</p>
                            @endif
                        </div>
                        @endif

                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('verification.appeals.show', $appeal->id) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                View Details
                            </a>
                            @if($appeal->isPending() || $appeal->isUnderReview())
                            <form method="POST" action="{{ route('verification.appeals.withdraw', $appeal->id) }}"
                                  onsubmit="return confirm('Are you sure you want to withdraw this appeal?')">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                    Withdraw
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                        <p class="text-gray-500">You haven't filed any appeals yet</p>
                    </div>
                    @endforelse

                    <div class="mt-4">
                        {{ $appeals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
