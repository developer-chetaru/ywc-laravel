<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Share Document: {{ $document->document_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Wizard Steps -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 text-center">
                                <div class="w-10 h-10 mx-auto bg-blue-600 text-white rounded-full flex items-center justify-center mb-2">
                                    <i class="fas fa-user"></i>
                                </div>
                                <p class="text-sm font-medium text-blue-600">Recipient</p>
                            </div>
                            <div class="flex-1 border-t-2 border-gray-300"></div>
                            <div class="flex-1 text-center">
                                <div class="w-10 h-10 mx-auto bg-gray-300 text-gray-600 rounded-full flex items-center justify-center mb-2">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-600">Permissions</p>
                            </div>
                            <div class="flex-1 border-t-2 border-gray-300"></div>
                            <div class="flex-1 text-center">
                                <div class="w-10 h-10 mx-auto bg-gray-300 text-gray-600 rounded-full flex items-center justify-center mb-2">
                                    <i class="fas fa-check"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-600">Review</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('share-wizard.create', $document->id) }}" method="POST" id="shareForm">
                        @csrf

                        <!-- Step 1: Recipient Info -->
                        <div id="step1" class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recipient Information</h3>

                            <!-- Use Template -->
                            @if($templates->count() > 0)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Use Template (Optional)</label>
                                <select name="template_id" id="templateSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    <option value="">Custom Settings</option>
                                    @foreach($templates as $template)
                                    <option value="{{ $template->id }}" 
                                            data-permissions="{{ json_encode([
                                                'can_download' => $template->can_download,
                                                'can_print' => $template->can_print,
                                                'can_share' => $template->can_share,
                                                'can_comment' => $template->can_comment,
                                                'is_one_time' => $template->is_one_time,
                                                'max_views' => $template->max_views,
                                                'require_password' => $template->require_password,
                                                'require_watermark' => $template->require_watermark,
                                                'duration_days' => $template->duration_days,
                                            ]) }}">
                                        {{ $template->name }} @if($template->is_default)<span class="text-blue-600">(Default)</span>@endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Email *</label>
                                <input type="email" name="recipient_email" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                       placeholder="john@example.com">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Name</label>
                                <input type="text" name="recipient_name"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                       placeholder="John Doe">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Personal Message</label>
                                <textarea name="personal_message" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                          placeholder="Optional message to recipient..."></textarea>
                            </div>

                            <div class="flex justify-end">
                                <button type="button" onclick="nextStep(2)"
                                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Next <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Permissions & Security -->
                        <div id="step2" class="space-y-6 hidden">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Permissions & Security</h3>

                            <!-- Permissions -->
                            <div class="bg-gray-50 p-4 rounded-md space-y-3">
                                <h4 class="font-medium text-gray-900">What can recipient do?</h4>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="can_download" value="1" checked class="mr-2">
                                    <span class="text-sm">Allow Download</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" name="can_print" value="1" checked class="mr-2">
                                    <span class="text-sm">Allow Print</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" name="can_share" value="1" class="mr-2">
                                    <span class="text-sm">Allow Re-sharing</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" name="can_comment" value="1" class="mr-2">
                                    <span class="text-sm">Allow Comments</span>
                                </label>
                            </div>

                            <!-- Access Control -->
                            <div class="bg-gray-50 p-4 rounded-md space-y-3">
                                <h4 class="font-medium text-gray-900">Access Control</h4>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_one_time" value="1" class="mr-2">
                                    <span class="text-sm">One-time access (link expires after first view)</span>
                                </label>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Views</label>
                                    <input type="number" name="max_views" min="1" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                           placeholder="Leave empty for unlimited">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Protection</label>
                                    <input type="password" name="password"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                           placeholder="Leave empty for no password">
                                </div>

                                <label class="flex items-center">
                                    <input type="checkbox" name="require_watermark" value="1" class="mr-2">
                                    <span class="text-sm">Add watermark to document</span>
                                </label>
                            </div>

                            <!-- Time Restrictions -->
                            <div class="bg-gray-50 p-4 rounded-md space-y-3">
                                <h4 class="font-medium text-gray-900">Time Restrictions</h4>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Expires In (Days)</label>
                                    <input type="number" name="expires_in_days" min="1" max="365" value="30"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Access Start Date</label>
                                    <input type="datetime-local" name="access_start_date"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Access End Date</label>
                                    <input type="datetime-local" name="access_end_date"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                </div>
                            </div>

                            <div class="flex justify-between">
                                <button type="button" onclick="nextStep(1)"
                                        class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    <i class="fas fa-arrow-left mr-2"></i> Back
                                </button>
                                <button type="button" onclick="nextStep(3)"
                                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Next <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Review -->
                        <div id="step3" class="space-y-6 hidden">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Review & Confirm</h3>

                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <h4 class="font-medium text-blue-900 mb-3">Share Summary</h4>
                                <div class="space-y-2 text-sm text-blue-800">
                                    <p><strong>Document:</strong> {{ $document->document_name }}</p>
                                    <p><strong>Recipient:</strong> <span id="reviewEmail"></span></p>
                                    <p><strong>Permissions:</strong> <span id="reviewPermissions"></span></p>
                                    <p><strong>Access Control:</strong> <span id="reviewAccess"></span></p>
                                    <p><strong>Expires:</strong> <span id="reviewExpiry"></span></p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                                <textarea name="share_notes" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                          placeholder="Internal notes about this share..."></textarea>
                            </div>

                            <div class="flex justify-between">
                                <button type="button" onclick="nextStep(2)"
                                        class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    <i class="fas fa-arrow-left mr-2"></i> Back
                                </button>
                                <button type="submit"
                                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    <i class="fas fa-share mr-2"></i> Create Share Link
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function nextStep(step) {
            // Hide all steps
            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step3').classList.add('hidden');

            // Show target step
            document.getElementById('step' + step).classList.remove('hidden');

            // Update step indicators
            updateStepIndicators(step);

            // If going to review, populate summary
            if (step === 3) {
                populateReview();
            }
        }

        function updateStepIndicators(activeStep) {
            // This can be enhanced to update the visual step indicators
        }

        function populateReview() {
            const email = document.querySelector('input[name="recipient_email"]').value;
            const permissions = [];
            
            if (document.querySelector('input[name="can_download"]').checked) permissions.push('Download');
            if (document.querySelector('input[name="can_print"]').checked) permissions.push('Print');
            if (document.querySelector('input[name="can_share"]').checked) permissions.push('Share');
            if (document.querySelector('input[name="can_comment"]').checked) permissions.push('Comment');

            const access = [];
            if (document.querySelector('input[name="is_one_time"]').checked) access.push('One-time');
            const maxViews = document.querySelector('input[name="max_views"]').value;
            if (maxViews) access.push(maxViews + ' views max');
            if (document.querySelector('input[name="password"]').value) access.push('Password protected');
            if (document.querySelector('input[name="require_watermark"]').checked) access.push('Watermarked');

            const expiryDays = document.querySelector('input[name="expires_in_days"]').value;

            document.getElementById('reviewEmail').textContent = email || 'Not specified';
            document.getElementById('reviewPermissions').textContent = permissions.join(', ') || 'View only';
            document.getElementById('reviewAccess').textContent = access.join(', ') || 'No restrictions';
            document.getElementById('reviewExpiry').textContent = expiryDays ? expiryDays + ' days' : 'Never';
        }

        // Template selection handler
        document.getElementById('templateSelect')?.addEventListener('change', function() {
            if (this.value) {
                const option = this.options[this.selectedIndex];
                const permissions = JSON.parse(option.dataset.permissions);
                
                // Apply template settings
                document.querySelector('input[name="can_download"]').checked = permissions.can_download;
                document.querySelector('input[name="can_print"]').checked = permissions.can_print;
                document.querySelector('input[name="can_share"]').checked = permissions.can_share;
                document.querySelector('input[name="can_comment"]').checked = permissions.can_comment;
                document.querySelector('input[name="is_one_time"]').checked = permissions.is_one_time;
                document.querySelector('input[name="max_views"]').value = permissions.max_views || '';
                document.querySelector('input[name="require_watermark"]').checked = permissions.require_watermark;
                document.querySelector('input[name="expires_in_days"]').value = permissions.duration_days || 30;
            }
        });
    </script>
    @endpush
</x-app-layout>
