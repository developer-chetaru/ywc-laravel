<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                OCR Accuracy Dashboard
            </h2>
            <a href="{{ route('ocr.accuracy.export') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <i class="fas fa-download mr-2"></i>Export Report
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Total Corrections</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_corrections'] ?? 0 }}</div>
                </div>
                <div class="bg-green-50 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-green-800">Average Accuracy</div>
                    <div class="text-2xl font-bold text-green-900">{{ number_format($stats['average_accuracy'] ?? 0, 1) }}%</div>
                </div>
                <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-blue-800">Max Accuracy</div>
                    <div class="text-2xl font-bold text-blue-900">{{ number_format($stats['max_accuracy'] ?? 0, 1) }}%</div>
                </div>
                <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-red-800">Fields Corrected</div>
                    <div class="text-2xl font-bold text-red-900">{{ $stats['total_fields_corrected'] ?? 0 }}</div>
                </div>
            </div>

            <!-- Recent Corrections -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Recent Corrections</h3>
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Accuracy</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fields</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentLogs as $log)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $log->document->document_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 rounded-full {{ $log->overall_accuracy >= 90 ? 'bg-green-100 text-green-800' : ($log->overall_accuracy >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($log->overall_accuracy, 1) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">{{ $log->fields_correct }}/{{ $log->fields_total }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $log->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No corrections yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Low Accuracy Alerts -->
            @if($lowAccuracyDocs->count() > 0)
            <div class="bg-red-50 border border-red-200 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-red-900 mb-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Low Accuracy Documents
                    </h3>
                    <div class="space-y-2">
                        @foreach($lowAccuracyDocs as $log)
                        <div class="flex justify-between items-center p-3 bg-white rounded">
                            <div>
                                <p class="font-medium">{{ $log->document->document_name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600">Accuracy: {{ number_format($log->overall_accuracy, 1) }}%</p>
                            </div>
                            <a href="{{ route('ocr.accuracy.show', $log->id) }}" class="text-red-600 hover:text-red-800">
                                Review <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
