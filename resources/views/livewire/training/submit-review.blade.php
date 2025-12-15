<div>
    <main class="flex-1">
        <div class="w-full bg-white p-5 rounded-md pb-10 max-w-3xl mx-auto">
            <h2 class="text-[#0053FF] text-[30px] font-semibold mb-6">Submit Review</h2>

            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-lg mb-2">{{ $course->certification->name }}</h3>
                <p class="text-gray-600">Provider: {{ $course->provider->name }}</p>
            </div>

            <form wire:submit.prevent="submit">
                <!-- Overall Rating -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">Overall Rating *</label>
                    <div class="flex items-center gap-2">
                        @for($i = 5; $i >= 1; $i--)
                            <input type="radio" wire:model="rating_overall" value="{{ $i }}" 
                                   id="overall_{{ $i }}" class="hidden">
                            <label for="overall_{{ $i }}" 
                                   class="cursor-pointer">
                                <svg class="w-8 h-8 {{ $rating_overall >= $i ? 'text-yellow-400' : 'text-gray-300' }}" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </label>
                        @endfor
                    </div>
                    @error('rating_overall') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Category Ratings -->
                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Course Content</label>
                        <select wire:model="rating_content" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">Instructor Knowledge</label>
                        <select wire:model="rating_instructor" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">Facilities & Equipment</label>
                        <select wire:model="rating_facilities" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">Value for Money</label>
                        <select wire:model="rating_value" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">Administration</label>
                        <select wire:model="rating_administration" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Would Recommend -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="would_recommend" class="mr-2">
                        <span class="font-semibold">I would recommend this course</span>
                    </label>
                </div>

                <!-- Review Text -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">Your Review</label>
                    <textarea wire:model="review_text" rows="4" 
                              placeholder="Share your experience..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]"></textarea>
                    @error('review_text') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- What did you like most -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">What did you like most?</label>
                    <textarea wire:model="liked_most" rows="3" 
                              placeholder="Tell us what stood out..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]"></textarea>
                </div>

                <!-- Areas for improvement -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">Areas for improvement</label>
                    <textarea wire:model="areas_for_improvement" rows="3" 
                              placeholder="How could this course be better?"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]"></textarea>
                </div>

                <!-- Tips for students -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">Tips for future students</label>
                    <textarea wire:model="tips_for_students" rows="3" 
                              placeholder="Any advice for others taking this course?"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]"></textarea>
                </div>

                <!-- Date Attended -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">Date Attended</label>
                    <input type="date" wire:model="date_attended" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0053FF]">
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4">
                    <button type="submit" 
                            class="flex-1 px-6 py-3 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700 font-semibold">
                        Submit Review
                    </button>
                    <a href="{{ route('training.certification.detail', $course->certification->slug) }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>
