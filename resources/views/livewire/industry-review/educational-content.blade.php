<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Educational Resources</h1>

            {{-- Tabs --}}
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button wire:click="setSection('brokers')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeSection === 'brokers' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Choosing a Broker
                    </button>
                    <button wire:click="setSection('contractors')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeSection === 'contractors' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Hiring Contractors
                    </button>
                    <button wire:click="setSection('yachts')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeSection === 'yachts' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Yacht Reviews
                    </button>
                </nav>
            </div>

            {{-- Content --}}
            @if($activeSection === 'brokers')
                <div class="space-y-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">What to Look for in a Good Broker</h2>
                        <ul class="space-y-3 text-gray-700">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Industry Certifications:</strong> Look for MYBA membership, licensed brokers, and verified agencies</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Years in Business:</strong> Established agencies (10+ years) often have better networks and experience</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Fee Structure Transparency:</strong> Clear explanation of who pays fees - legitimate agencies are transparent</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Specializations Match Your Needs:</strong> Some agencies specialize in certain yacht types or positions</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Post-Placement Support:</strong> Good brokers check in after placement and help with issues</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Red Flags to Avoid</h2>
                        <ul class="space-y-3 text-gray-700">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Upfront Fees for Crew:</strong> Legitimate agencies don't charge crew members upfront fees</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Pressure Tactics:</strong> Be wary of brokers who pressure you to accept positions quickly</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Vague Job Descriptions:</strong> Reputable brokers provide detailed, accurate job specifications</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>No Verifiable Track Record:</strong> Check reviews and ask for references</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Poor Communication Patterns:</strong> If they don't respond promptly during initial contact, it won't improve</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Questions to Ask a Broker</h2>
                        <ul class="space-y-3 text-gray-700">
                            <li>• Who pays your placement fee?</li>
                            <li>• What's your placement success rate?</li>
                            <li>• How long have you been in business?</li>
                            <li>• What's your average placement time?</li>
                            <li>• Do you provide post-placement support?</li>
                            <li>• Can you provide references from placed crew?</li>
                            <li>• What's your specialization (yacht types, positions)?</li>
                            <li>• Are you licensed and/or MYBA member?</li>
                        </ul>
                    </div>
                </div>
            @elseif($activeSection === 'contractors')
                <div class="space-y-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Choosing the Right Contractor</h2>
                        <ul class="space-y-3 text-gray-700">
                            <li>• Check reviews for quality of work, professionalism, and timeliness</li>
                            <li>• Verify they have experience with your specific yacht type/size</li>
                            <li>• Get multiple quotes and compare pricing transparency</li>
                            <li>• Ask for references and examples of previous work</li>
                            <li>• Ensure they have proper insurance and certifications</li>
                            <li>• Check if they offer emergency service if needed</li>
                            <li>• Verify their service area covers your location</li>
                        </ul>
                    </div>
                </div>
            @elseif($activeSection === 'yachts')
                <div class="space-y-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Understanding Yacht Reviews</h2>
                        <ul class="space-y-3 text-gray-700">
                            <li>• Read multiple reviews to get a balanced perspective</li>
                            <li>• Pay attention to category ratings (Yacht Quality, Crew Culture, Management, Benefits)</li>
                            <li>• Look for reviews from crew in similar positions to yours</li>
                            <li>• Consider the date of reviews - conditions can change</li>
                            <li>• Check if management responds to reviews professionally</li>
                            <li>• Look for patterns in positive and negative feedback</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
