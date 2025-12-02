<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Compare Brokers</h1>

            {{-- Broker Selection --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                @for($i = 1; $i <= 3; $i++)
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Broker {{ $i }}</label>
                        @if(${'broker' . $i})
                            <div class="bg-blue-50 rounded-lg p-3 mb-2">
                                <p class="font-semibold">{{ ${'broker' . $i}->name }}</p>
                                <button wire:click="removeBroker({{ $i }})" class="text-red-600 text-sm mt-1">Remove</button>
                            </div>
                        @else
                            <select wire:change="selectBroker({{ $i }}, $event.target.value)" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="">Select a broker...</option>
                                @foreach($availableBrokers as $broker)
                                    <option value="{{ $broker->id }}">{{ $broker->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                @endfor
            </div>

            {{-- Comparison Table --}}
            @if($broker1 || $broker2 || $broker3)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metric</th>
                                @if($broker1)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $broker1->name }}</th>
                                @endif
                                @if($broker2)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $broker2->name }}</th>
                                @endif
                                @if($broker3)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $broker3->name }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Overall Rating</td>
                                @if($broker1)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($broker1->rating_avg, 1) }}/5 ⭐</td>
                                @endif
                                @if($broker2)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($broker2->rating_avg, 1) }}/5 ⭐</td>
                                @endif
                                @if($broker3)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($broker3->rating_avg, 1) }}/5 ⭐</td>
                                @endif
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Total Reviews</td>
                                @if($broker1)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker1->reviews_count }}</td>
                                @endif
                                @if($broker2)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker2->reviews_count }}</td>
                                @endif
                                @if($broker3)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker3->reviews_count }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Fee Structure</td>
                                @if($broker1)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ ucfirst(str_replace('_', ' ', $broker1->fee_structure ?? 'N/A')) }}</td>
                                @endif
                                @if($broker2)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ ucfirst(str_replace('_', ' ', $broker2->fee_structure ?? 'N/A')) }}</td>
                                @endif
                                @if($broker3)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ ucfirst(str_replace('_', ' ', $broker3->fee_structure ?? 'N/A')) }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Years in Business</td>
                                @if($broker1)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker1->years_in_business ?? 'N/A' }}</td>
                                @endif
                                @if($broker2)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker2->years_in_business ?? 'N/A' }}</td>
                                @endif
                                @if($broker3)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker3->years_in_business ?? 'N/A' }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Verified</td>
                                @if($broker1)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker1->is_verified ? '✓ Yes' : '✗ No' }}</td>
                                @endif
                                @if($broker2)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker2->is_verified ? '✓ Yes' : '✗ No' }}</td>
                                @endif
                                @if($broker3)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker3->is_verified ? '✓ Yes' : '✗ No' }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">MYBA Member</td>
                                @if($broker1)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker1->is_myba_member ? '✓ Yes' : '✗ No' }}</td>
                                @endif
                                @if($broker2)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker2->is_myba_member ? '✓ Yes' : '✗ No' }}</td>
                                @endif
                                @if($broker3)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker3->is_myba_member ? '✓ Yes' : '✗ No' }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Success Rate</td>
                                @if($broker1)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker1->success_rate ? number_format($broker1->success_rate, 1) . '%' : 'N/A' }}</td>
                                @endif
                                @if($broker2)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker2->success_rate ? number_format($broker2->success_rate, 1) . '%' : 'N/A' }}</td>
                                @endif
                                @if($broker3)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $broker3->success_rate ? number_format($broker3->success_rate, 1) . '%' : 'N/A' }}</td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <p>Select at least one broker to compare</p>
                </div>
            @endif
        </div>
    </div>
</div>
