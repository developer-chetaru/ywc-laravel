<div>
    <main class="flex-1">
        <div class="w-full bg-white p-5 rounded-md pb-10">
            <h2 class="text-[#0053FF] text-[30px] font-semibold mb-6">Career Pathways</h2>

            @if($certification)
                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <h3 class="font-semibold text-lg mb-2">Pathways for: {{ $certification->name }}</h3>
                </div>
            @endif

            @forelse($pathways as $pathway)
                <div class="mb-8 border rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-[#0053FF] mb-2">{{ $pathway->name }}</h3>
                            <p class="text-gray-600 mb-2">{{ $pathway->description }}</p>
                            <div class="flex gap-4 text-sm text-gray-600">
                                <div>
                                    <span class="font-semibold">From:</span> {{ $pathway->starting_position }}
                                </div>
                                <div>
                                    <span class="font-semibold">To:</span> {{ $pathway->target_position }}
                                </div>
                                @if($pathway->estimated_timeline_months)
                                    <div>
                                        <span class="font-semibold">Timeline:</span> {{ $pathway->estimated_timeline_months }} months
                                    </div>
                                @endif
                                @if($pathway->estimated_total_cost)
                                    <div>
                                        <span class="font-semibold">Est. Cost:</span> £{{ number_format($pathway->estimated_total_cost, 2) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Certification Sequence Visualization -->
                    <div class="mt-6">
                        <h4 class="font-semibold mb-4">Certification Progression</h4>
                        <div class="flex flex-wrap gap-4 items-center">
                            @php
                                $certifications = $pathway->getCertifications();
                            @endphp
                            @foreach($certifications as $index => $cert)
                                <div class="flex items-center">
                                    <div class="relative">
                                        <div class="w-48 p-4 rounded-lg border-2 
                                            @if($this->isCertificationCompleted($cert->id)) 
                                                border-green-500 bg-green-50 
                                            @else 
                                                border-gray-300 bg-white 
                                            @endif">
                                            <div class="flex items-center gap-2 mb-2">
                                                @if($this->isCertificationCompleted($cert->id))
                                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                                <h5 class="font-semibold text-sm">{{ $cert->name }}</h5>
                                            </div>
                                            @if($cert->official_designation)
                                                <p class="text-xs text-gray-600">{{ $cert->official_designation }}</p>
                                            @endif
                                        </div>
                                        @if($index < $certifications->count() - 1)
                                            <div class="absolute -right-6 top-1/2 transform -translate-y-1/2">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($pathway->career_benefits)
                        <div class="mt-4">
                            <h4 class="font-semibold mb-2">Career Benefits</h4>
                            <p class="text-gray-700">{{ $pathway->career_benefits }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-12 border rounded-lg">
                    <p class="text-gray-500 text-lg">No career pathways available yet.</p>
                </div>
            @endforelse
        </div>
    </main>
</div>
