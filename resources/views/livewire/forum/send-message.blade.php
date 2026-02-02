<div>
    {{-- Trigger Button (only show when recipient is specified, not in welcome section) --}}
    @if (!$showModal && $recipientId)
        <button wire:click="openModal({{ $recipientId }}, '{{ $recipientName }}')"
            class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            Send Message
        </button>
    @endif

    {{-- Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click="closeModal">
            <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col" wire:click.stop>
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-900">Send Private Message</h3>
                        <button wire:click="closeModal" 
                            wire:loading.attr="disabled"
                            wire:target="send"
                            class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6 overflow-y-auto flex-1">
                    {{-- Flash Messages --}}
                    @if (session()->has('success'))
                        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="send" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                To <span class="text-red-500">*</span>
                            </label>
                            @if ($recipientId)
                                <input type="text" value="{{ $recipientName }}" disabled
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-700 focus:outline-none">
                                <input type="hidden" wire:model="recipientId">
                            @else
                                <select wire:model="recipientId" required
                                    wire:loading.attr="disabled"
                                    wire:target="send"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">Select recipient...</option>
                                    @foreach (\App\Models\User::where('id', '!=', Auth::id())->orderBy('first_name')->orderBy('last_name')->get() as $user)
                                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('recipientId') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject (Optional)</label>
                            <input type="text" wire:model="subject"
                                wire:loading.attr="disabled"
                                wire:target="send"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition disabled:opacity-50 disabled:cursor-not-allowed"
                                placeholder="Message subject...">
                            @error('subject') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Message <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="content" rows="6"
                                wire:loading.attr="disabled"
                                wire:target="send"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition resize-none disabled:opacity-50 disabled:cursor-not-allowed"
                                placeholder="Type your message here..."></textarea>
                            @error('content') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Attachments (Max 5MB each)
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition">
                                <div class="space-y-1 text-center w-full">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload files</span>
                                            <input type="file" wire:model="attachments" multiple class="sr-only" accept="*/*">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, PDF up to 5MB</p>
                                </div>
                            </div>
                            @error('attachments.*') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                            @if ($attachments)
                                <div class="mt-3 space-y-2">
                                    @foreach ($attachments as $index => $file)
                                        <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg p-3">
                                            <div class="flex items-center space-x-3">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-sm text-gray-700 font-medium">{{ $file->getClientOriginalName() }}</span>
                                                <span class="text-xs text-gray-500">({{ number_format($file->getSize() / 1024, 2) }} KB)</span>
                                            </div>
                                            <button type="button" wire:click="removeAttachment({{ $index }})"
                                                class="text-red-600 hover:text-red-800 text-sm font-medium px-2 py-1 rounded hover:bg-red-50 transition">
                                                Remove
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="button" wire:click="closeModal" 
                                wire:loading.attr="disabled"
                                wire:target="send"
                                class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                                Cancel
                            </button>
                            <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:target="send"
                                class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm hover:shadow disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                                <span wire:loading.remove wire:target="send">Send Message</span>
                                <span wire:loading wire:target="send" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Sending...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
