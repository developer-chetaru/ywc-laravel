<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->document_name }} - YWC Shared Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @if(!$share->can_print)
    <style>
        @media print {
            body { display: none !important; }
        }
    </style>
    @endif
</head>
<body class="bg-gray-100" @if(!$share->can_print)oncontextmenu="return false"@endif>
    <div class="min-h-screen py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-file-alt text-blue-600 mr-3"></i>
                            {{ $document->document_name }}
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Shared by {{ $share->user->first_name }} {{ $share->user->last_name }}
                        </p>
                    </div>
                    <div class="text-right">
                        @if($share->can_download)
                        <a href="{{ route('shared.download', $token) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-download mr-2"></i>Download
                        </a>
                        @endif
                    </div>
                </div>

                @if($share->personal_message)
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-comment-dots mr-2"></i>{{ $share->personal_message }}
                    </p>
                </div>
                @endif
            </div>

            <!-- Document Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Document Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Document Number</p>
                        <p class="font-medium text-gray-900">{{ $document->document_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Category</p>
                        <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $document->category)) }}</p>
                    </div>
                    @if($document->issue_date)
                    <div>
                        <p class="text-sm text-gray-600">Issue Date</p>
                        <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($document->issue_date)->format('d M Y') }}</p>
                    </div>
                    @endif
                    @if($document->expiry_date)
                    <div>
                        <p class="text-sm text-gray-600">Expiry Date</p>
                        <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($document->expiry_date)->format('d M Y') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Access Restrictions -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        @if($share->is_one_time)
                            This is a one-time access link. It will expire after you close this page.
                        @elseif($share->max_views)
                            This link has been viewed {{ $share->view_count }} of {{ $share->max_views }} times.
                        @endif
                        @if($share->expires_at)
                            This link expires on {{ $share->expires_at->format('d M Y H:i') }}.
                        @endif
                    </p>
                </div>
            </div>

            <!-- Document Preview -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Document Preview</h2>
                
                @if($share->require_watermark)
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-certificate mr-2"></i>This document is watermarked
                    </p>
                </div>
                @endif

                <div class="border border-gray-300 rounded-md overflow-hidden bg-gray-50">
                    @php
                        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                    @endphp

                    @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="{{ Storage::url($document->file_path) }}" alt="{{ $document->document_name }}" class="w-full">
                    @elseif($extension === 'pdf')
                        <iframe src="{{ Storage::url($document->file_path) }}" class="w-full h-screen"></iframe>
                    @else
                        <div class="p-8 text-center">
                            <i class="fas fa-file text-gray-400 text-6xl mb-4"></i>
                            <p class="text-gray-600">Preview not available for this file type.</p>
                            @if($share->can_download)
                            <a href="{{ route('shared.download', $token) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 mt-4">
                                <i class="fas fa-download mr-2"></i>Download to View
                            </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Secured by YWC - Your credentials, verified globally
                </p>
            </div>
        </div>
    </div>

    @if(!$share->can_print)
    <script>
        // Disable right-click
        document.addEventListener('contextmenu', event => event.preventDefault());
        
        // Disable common keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && (e.key === 'p' || e.key === 's' || e.key === 'c')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
    @endif
</body>
</html>
