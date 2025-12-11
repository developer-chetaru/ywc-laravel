<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📋 Tax Planning & Analysis</h1>
                    <p class="text-gray-600 mt-1">Understand your tax residency and obligations</p>
                </div>
                <a href="{{ route('financial.dashboard') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Dashboard
                </a>
            </div>

            @if(session('message'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit.prevent="analyze" class="space-y-6">
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Tax Residency Questionnaire</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nationality *</label>
                            <input type="text" wire:model="nationality" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Residence *</label>
                            <input type="text" wire:model="current_residence" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Permanent Address</label>
                            <input type="text" wire:model="permanent_address" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Employment Contract Location</label>
                            <input type="text" wire:model="employment_contract_location" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <button type="submit" 
                            class="mt-6 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Analyze Tax Residency
                    </button>
                </div>
            </form>

            @if($taxResidencyAnalysis)
            <div class="mt-6 space-y-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Tax Residency Determination</h2>
                    @foreach($taxResidencyAnalysis as $country => $status)
                    <div class="mb-3">
                        <div class="font-semibold text-gray-900">{{ $country }}</div>
                        <div class="text-gray-700">{{ $status }}</div>
                    </div>
                    @endforeach
                </div>

                @if(!empty($taxObligations))
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Tax Obligations</h2>
                    @foreach($taxObligations as $country => $obligation)
                    <div class="mb-3">
                        <div class="font-semibold text-gray-900">{{ $country }}</div>
                        <div class="text-gray-700">{{ $obligation }}</div>
                    </div>
                    @endforeach
                </div>
                @endif

                @if(!empty($optimizationOpportunities))
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Optimization Opportunities</h2>
                    <ul class="list-disc list-inside space-y-2">
                        @foreach($optimizationOpportunities as $opportunity)
                        <li class="text-gray-700">{{ $opportunity }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

