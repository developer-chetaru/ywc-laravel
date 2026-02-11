@extends('layouts.app-laravel')

@section('content')
<div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Share Documents</h1>
                    <p class="text-gray-600 mt-1">Create a share link to securely share your documents</p>
                </div>
                <a href="{{ route('documents') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Documents
                </a>
            </div>
        </div>

        <!-- Share Form -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <!-- Validation Error Summary -->
            <div id="shareValidationErrors" class="hidden mb-4 p-4 bg-red-50 border-l-4 border-red-400 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul id="shareErrorList" class="list-disc list-inside space-y-1"></ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipient Email (Optional) - Multiple Emails Support -->
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium">
                    <i class="fas fa-envelope mr-1 text-blue-600"></i>Recipient Email(s) <span class="text-gray-400 font-normal">(Optional)</span>
                </label>
                <div class="border border-gray-300 rounded-lg p-2 min-h-[50px] bg-white">
                    <div id="recipientEmailTags" class="flex flex-wrap gap-2 mb-2"></div>
                    <input type="text" id="recipientEmail" placeholder="Enter email addresses (comma or space separated)" class="w-full border-0 p-1 outline-none focus:ring-0" />
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i><strong>Purpose:</strong> Send email notifications to recipients when share is created. You can add multiple emails (max 10). Leave empty to create share link without sending emails.
                </p>
                <p id="recipientEmailError" class="text-red-600 text-xs mt-1 hidden"></p>
            </div>

            <!-- Message -->
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium">Message</label>
                <textarea id="messageInput" class="w-full border p-2 rounded outline-none" rows="3" placeholder="Enter message (optional)"></textarea>
            </div>

            <!-- Expiry Options -->
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium">Link Expiry</label>
                <select id="expiryOption" class="w-full border p-2 rounded outline-none">
                    <option value="7">7 Days</option>
                    <option value="30" selected>30 Days</option>
                    <option value="permanent">Permanent (No Expiry)</option>
                </select>
            </div>

            <!-- Security Options -->
            <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h4 class="text-sm font-semibold mb-3">Security Options</h4>
                <p class="text-xs text-gray-600 mb-3 bg-blue-50 p-2 rounded border border-blue-200">
                    <i class="fas fa-shield-alt mr-1 text-blue-600"></i><strong>Note:</strong> You can revoke access to any share link at any time from the success screen or your shares management page.
                </p>
                
                <!-- Password Protection -->
                <div class="mb-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="enablePassword" class="mr-2" />
                        <span class="text-sm font-medium">Password Protection</span>
                    </label>
                    <div id="passwordInputWrapper" class="mt-2" style="display: none;">
                        <input type="password" id="sharePassword" placeholder="Enter password (min 6 characters)" class="w-full border border-gray-300 p-2 rounded outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" minlength="6" />
                    </div>
                </div>

                <!-- Email Restriction -->
                <div class="mb-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="enableEmailRestriction" class="mr-2" />
                        <span class="text-sm font-medium">Restrict to Specific Email</span>
                    </label>
                    <div id="emailRestrictionWrapper" class="mt-2" style="display: none;">
                        <input type="email" id="restrictToEmail" placeholder="Only this email can access" class="w-full border border-gray-300 p-2 rounded outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        <p class="text-xs text-orange-600 bg-orange-50 p-2 rounded mt-2 border border-orange-200" id="emailRestrictionHint">
                            <i class="fas fa-shield-alt mr-1"></i><strong>Security Feature:</strong> Only the specified email address will be able to access this share link. This is different from "Recipient Email" - this restricts who can VIEW the share, while "Recipient Email" only sends notifications.
                        </p>
                    </div>
                </div>

                <!-- Generate QR Code -->
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="generateQrCode" class="mr-2" />
                        <span class="text-sm font-medium">Generate QR Code</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">Create a QR code for easy sharing</p>
                </div>
            </div>

            <!-- Document Selection -->
            <div class="mb-4">
                <label class="block text-lg font-semibold mb-3">Select Documents <span class="text-red-500">*</span></label>
                <p id="documentsError" class="text-red-600 text-sm mt-1 mb-2 hidden"></p>
                <div id="docList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($share_documents as $doc)
                    <div class="docCard border rounded-lg p-3 cursor-pointer hover:shadow-lg transition-shadow duration-200 ease-in-out bg-white flex flex-row items-center" data-id="{{ $doc->id }}">
                        <div class="w-16 h-16 bg-gray-100 flex items-center justify-center rounded-md overflow-hidden mr-3">
                            @if(strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION)) === 'pdf')
                                <img src="{{ asset('images/pdf.png') }}" alt="PDF" class="h-10 w-10 object-contain" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-file-pdf text-red-500 text-3xl\'></i>';">
                            @else
                                <img src="{{ asset('storage/' . $doc->file_path) }}" alt="{{ $doc->type }}" class="object-contain h-full w-full" onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-file-image text-gray-400 text-3xl\'></i>';">
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold truncate" title="{{ $doc->type }}">{{ $doc->type }}</h4>
                            <span class="text-xs text-gray-500">Featured on profile</span>
                        </div>
                        <input type="checkbox" name="documents[]" value="{{ $doc->id }}" class="hidden docCheckbox">
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Share Button -->
            <button type="button" id="saveShareBtn" class="bg-blue-600 text-white px-6 py-3 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center w-full hover:bg-blue-700 transition-colors font-medium">
                <span id="shareBtnText">Create Share Link</span>
                <svg id="shareBtnSpinner" class="animate-spin h-5 w-5 text-white ml-2 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Share Success Modal -->
<div id="shareSuccessModal" class="popup hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl w-[600px] max-h-[90vh] overflow-y-auto p-6 relative">
        <!-- Close Button -->
        <button class="closePopup absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl">&times;</button>

        <!-- Title -->
        <h3 class="text-xl font-semibold mb-4 text-green-600">
            <i class="fas fa-check-circle mr-2"></i>Share Created Successfully!
        </h3>

        <!-- Success Content -->
        <div class="space-y-4">
            <!-- Share Link -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Share Link:</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="shareUrlDisplay" readonly class="flex-1 px-3 py-2 border border-gray-300 rounded text-sm bg-gray-50" />
                    <button onclick="copyShareUrl()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-copy mr-1"></i> Copy
                    </button>
                </div>
            </div>

            <!-- QR Code -->
            <div id="qrCodeDisplay" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">QR Code:</label>
                <div class="flex flex-col items-center gap-4 p-4 bg-gray-50 rounded-lg">
                    <img id="qrCodeImage" src="" alt="QR Code" class="w-48 h-48 border-2 border-gray-300 rounded-lg shadow-md" />
                    <button onclick="downloadQRCode()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm font-medium">
                        <i class="fas fa-download mr-1"></i> Download QR Code
                    </button>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4 border-t">
                <a id="visitShareLink" href="#" target="_blank" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-center text-sm font-medium">
                    <i class="fas fa-external-link-alt mr-1"></i> Visit Share Link
                </a>
                <button id="revokeShareBtn" onclick="revokeShareAccess()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                    <i class="fas fa-ban mr-1"></i> Revoke Access
                </button>
                <button onclick="closeShareSuccessModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 text-sm font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Multiple Recipient Emails Handler for Share Documents
    const recipientEmailInput = $("#recipientEmail");
    const recipientEmailTags = $("#recipientEmailTags");
    const recipientEmailError = $("#recipientEmailError");
    
    let recipientEmails = [];
    const maxRecipientEmails = 10;
    
    // Function to validate email
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Render recipient email tags
    function renderRecipientEmailTags() {
        recipientEmailTags.empty();
        recipientEmails.forEach((email, index) => {
            const tag = $(`
                <span class="bg-blue-500 text-white px-2 py-1 rounded text-sm flex items-center gap-1">
                    ${email} <span class="cursor-pointer hover:text-red-200 remove-recipient-email" data-index="${index}">&times;</span>
                </span>
            `);
            recipientEmailTags.append(tag);
        });
        recipientEmailError.addClass("hidden");
    }
    
    // Remove recipient email
    $(document).on("click", ".remove-recipient-email", function() {
        const index = $(this).data("index");
        recipientEmails.splice(index, 1);
        renderRecipientEmailTags();
    });
    
    // Process recipient email input
    function processRecipientEmailInput() {
        let value = recipientEmailInput.val().trim();
        if (!value) return;
        
        const splitEmails = value.split(/[\s,]+/);
        let errorMessage = "";
        
        splitEmails.forEach(e => {
            const email = e.trim();
            if (email && isValidEmail(email)) {
                if (recipientEmails.length < maxRecipientEmails) {
                    if (!recipientEmails.includes(email.toLowerCase())) {
                        recipientEmails.push(email.toLowerCase());
                    } else {
                        errorMessage = `Email already added: ${email}`;
                    }
                } else {
                    errorMessage = `Maximum ${maxRecipientEmails} emails allowed.`;
                }
            } else if (email) {
                errorMessage = `Invalid email format: ${email}`;
            }
        });
        
        if (errorMessage) {
            recipientEmailError.text(errorMessage).removeClass("hidden");
        } else {
            recipientEmailError.addClass("hidden");
        }
        
        recipientEmailInput.val('');
        renderRecipientEmailTags();
    }
    
    // Add email on Enter, comma, or space
    recipientEmailInput.on("keydown", function(e) {
        if (["Enter", ",", " "].includes(e.key)) {
            e.preventDefault();
            processRecipientEmailInput();
        }
    });
    
    // Process on blur
    recipientEmailInput.on("blur", function() {
        processRecipientEmailInput();
    });

    // Toggle password input visibility
    function togglePasswordField() {
        const passwordWrapper = $("#passwordInputWrapper");
        const passwordInput = $("#sharePassword");
        const isChecked = $("#enablePassword").is(":checked");
        
        if (isChecked) {
            passwordWrapper.show();
            passwordInput.prop("required", true);
        } else {
            passwordWrapper.hide();
            passwordInput.prop("required", false).val("");
        }
    }
    
    $("#enablePassword").on("change", function() {
        const passwordWrapper = $("#passwordInputWrapper");
        const passwordInput = $("#sharePassword");
        
        if ($(this).is(":checked")) {
            passwordWrapper.slideDown(300, function() {
                passwordInput.prop("required", true).focus();
            });
        } else {
            passwordWrapper.slideUp(300, function() {
                passwordInput.prop("required", false).val("");
            });
        }
    });
    
    // Check initial state on page load
    togglePasswordField();

    // Toggle email restriction input visibility
    function toggleEmailRestrictionField() {
        const emailWrapper = $("#emailRestrictionWrapper");
        const emailInput = $("#restrictToEmail");
        const isChecked = $("#enableEmailRestriction").is(":checked");
        
        if (isChecked) {
            emailWrapper.show();
            emailInput.prop("required", true);
        } else {
            emailWrapper.hide();
            emailInput.prop("required", false).val("");
        }
    }
    
    $("#enableEmailRestriction").on("change", function() {
        const emailWrapper = $("#emailRestrictionWrapper");
        const emailInput = $("#restrictToEmail");
        
        if ($(this).is(":checked")) {
            emailWrapper.slideDown(300, function() {
                emailInput.prop("required", true).focus();
            });
        } else {
            emailWrapper.slideUp(300, function() {
                emailInput.prop("required", false).val("");
            });
        }
    });
    
    // Check initial state on page load
    toggleEmailRestrictionField();

    // Document selection
    $(".docCard").on("click", function() {
        $(this).toggleClass("selected bg-blue-100 border-blue-500");
        if ($(".docCard.selected").length > 0) {
            $("#documentsError").addClass("hidden");
        }
    });

    // Show validation errors
    function showShareValidationErrors(errors) {
        const errorContainer = $("#shareValidationErrors");
        const errorList = $("#shareErrorList");
        
        if (errors.length > 0) {
            errorList.empty();
            errors.forEach(error => {
                errorList.append(`<li>${error}</li>`);
            });
            errorContainer.removeClass("hidden");
            errorContainer[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            errorContainer.addClass("hidden");
        }
    }

    // Hide all validation errors
    function hideShareValidationErrors() {
        $("#shareValidationErrors").addClass("hidden");
        $("#recipientEmailError").addClass("hidden");
        $("#documentsError").addClass("hidden");
    }

    // Store share data globally
    let currentShareData = null;
    let currentShareId = null;

    // Copy share URL function
    window.copyShareUrl = function(event) {
        event = event || window.event;
        const urlInput = document.getElementById("shareUrlDisplay");
        if (!urlInput) {
            console.error('Share URL input not found');
            return;
        }
        urlInput.select();
        urlInput.setSelectionRange(0, 99999);
        
        try {
            document.execCommand("copy");
            const copyBtn = event ? event.target.closest('button') : document.querySelector('button[onclick*="copyShareUrl"]');
            if (copyBtn) {
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
                copyBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                copyBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                
                setTimeout(function() {
                    copyBtn.innerHTML = originalText;
                    copyBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                    copyBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }, 2000);
            }
        } catch (err) {
            console.error('Failed to copy:', err);
            alert('Failed to copy. Please select and copy manually.');
        }
    }

    // Download QR code function
    window.downloadQRCode = function() {
        const qrImage = document.getElementById("qrCodeImage");
        if (!qrImage || !qrImage.src) {
            alert('QR code not available');
            return;
        }
        const link = document.createElement("a");
        link.href = qrImage.src;
        link.download = "share-qr-code.png";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Close share success modal function
    window.closeShareSuccessModal = function() {
        $("#shareSuccessModal").addClass("hidden").removeClass("flex");
        // Redirect to documents page
        window.location.href = "{{ route('documents') }}";
    }

    // Revoke share access function
    window.revokeShareAccess = function() {
        if (!currentShareId) {
            alert('Share ID not found. Cannot revoke access.');
            return;
        }
        
        if (!confirm('Are you sure you want to revoke access to this share link? Once revoked, the link will no longer be accessible.')) {
            return;
        }
        
        const revokeBtn = $("#revokeShareBtn");
        const originalText = revokeBtn.html();
        revokeBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Revoking...');
        
        $.ajax({
            url: `/shares/documents/${currentShareId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            success: function(response) {
                if (response && response.success) {
                    alert('Share access revoked successfully. The link is no longer accessible.');
                    $("#visitShareLink").addClass('opacity-50 cursor-not-allowed').prop('onclick', 'return false;');
                    revokeBtn.html('<i class="fas fa-check mr-1"></i> Revoked').addClass('bg-gray-500 hover:bg-gray-600').removeClass('bg-red-600 hover:bg-red-700');
                } else {
                    alert('Failed to revoke access: ' + (response?.message || 'Unknown error'));
                    revokeBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || xhr.responseText || 'Failed to revoke access. Please try again.';
                alert('Error: ' + errorMsg);
                revokeBtn.prop('disabled', false).html(originalText);
            }
        });
    }

    // Handle share button click
    $("#saveShareBtn").on("click", function(e) {
        e.preventDefault();
        handleShareButtonClick();
        return false;
    });

    // Share button click handler
    function handleShareButtonClick() {
        hideShareValidationErrors();

        const validationErrors = [];

        // Validate documents
        const selectedDocs = $(".docCard.selected");
        if (selectedDocs.length === 0) {
            validationErrors.push("Please select at least one document.");
            $("#documentsError").text("Please select at least one document.").removeClass("hidden");
        }

        // Validate password if enabled
        if ($("#enablePassword").is(":checked")) {
            const password = $("#sharePassword").val();
            if (!password || password.length < 6) {
                validationErrors.push("Password must be at least 6 characters.");
            }
        }

        // Validate email restriction if enabled
        if ($("#enableEmailRestriction").is(":checked")) {
            const restrictEmail = $("#restrictToEmail").val();
            if (!restrictEmail || !isValidEmail(restrictEmail)) {
                validationErrors.push("Please enter a valid email address for restriction.");
            }
        }

        if (validationErrors.length > 0) {
            showShareValidationErrors(validationErrors);
            return;
        }

        // Prepare form data
        const documentIds = [];
        selectedDocs.each(function() {
            documentIds.push($(this).find(".docCheckbox").val());
        });

        // Process any remaining email input
        processRecipientEmailInput();
        const recipientEmailsArray = recipientEmails.length > 0 ? recipientEmails : null;
        const personalMessage = $("#messageInput").val()?.trim() || null;
        
        const requestData = {
            document_ids: documentIds,
            recipient_emails: recipientEmailsArray,
            personal_message: personalMessage,
            expiry_option: $("#expiryOption").val(),
            generate_qr_code: $("#generateQrCode").is(":checked"),
        };

        // Add password if enabled
        if ($("#enablePassword").is(":checked")) {
            requestData.password = $("#sharePassword").val();
        }

        // Add email restriction if enabled
        if ($("#enableEmailRestriction").is(":checked")) {
            requestData.restrict_to_email = $("#restrictToEmail").val();
        }

        // Show processing
        $("#shareBtnText").text("Creating Share Link...");
        $("#shareBtnSpinner").removeClass("hidden");
        $("#saveShareBtn").prop("disabled", true);

        const shareUrl = "{{ route('shares.documents.store') }}";
        
        $.ajax({
            url: shareUrl,
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            data: JSON.stringify(requestData),
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    // Store share data and ID
                    currentShareData = response.share;
                    currentShareId = response.share?.id || null;
                    
                    // Populate success modal
                    if (response.share_url) {
                        $("#shareUrlDisplay").val(response.share_url);
                        $("#visitShareLink").attr("href", response.share_url);
                    }
                    
                    // Show QR code if generated
                    if (response.qr_code_url) {
                        $("#qrCodeImage").attr("src", response.qr_code_url);
                        $("#qrCodeDisplay").removeClass("hidden");
                    } else {
                        $("#qrCodeDisplay").addClass("hidden");
                    }
                    
                    // Show success modal
                    $("#shareSuccessModal").removeClass("hidden").addClass("flex");
                } else {
                    const errorMsg = response?.message || "Failed to create share. Unknown error.";
                    alert("Failed to create share: " + errorMsg);
                }
            },
            error: function(xhr, status, error) {
                const errors = xhr.responseJSON?.errors || {};
                const serverErrors = [];

                if (errors.document_ids) {
                    $("#documentsError").text(Array.isArray(errors.document_ids) ? errors.document_ids[0] : errors.document_ids).removeClass("hidden");
                    serverErrors.push(Array.isArray(errors.document_ids) ? errors.document_ids[0] : errors.document_ids);
                }

                if (errors.password) {
                    serverErrors.push(Array.isArray(errors.password) ? errors.password[0] : errors.password);
                }

                if (errors.restrict_to_email) {
                    serverErrors.push(Array.isArray(errors.restrict_to_email) ? errors.restrict_to_email[0] : errors.restrict_to_email);
                }

                if (serverErrors.length > 0) {
                    showShareValidationErrors(serverErrors);
                } else {
                    const errorMsg = xhr.responseJSON?.message || xhr.responseText || "Failed to create share link. Please try again.";
                    alert(errorMsg);
                }
            },
            complete: function() {
                $("#shareBtnText").text("Create Share Link");
                $("#shareBtnSpinner").addClass("hidden");
                $("#saveShareBtn").prop("disabled", false);
            }
        });
    }

    // Close popup handler
    $(document).on("click", ".closePopup", function() {
        $("#shareSuccessModal").addClass("hidden").removeClass("flex");
    });
});
</script>
@endpush
@endsection
