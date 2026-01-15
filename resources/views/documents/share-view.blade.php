<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Documents - YWC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400;1,500;1,600&display=swap');
        body { font-family: 'DM Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-4xl mx-auto">
            {{-- Header --}}
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Shared Documents</h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Shared by <strong>{{ $sender->first_name }} {{ $sender->last_name }}</strong>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Views: {{ $share->view_count }}</p>
                        @if($share->expires_at)
                        <p class="text-sm text-gray-600">
                            Expires: {{ \Carbon\Carbon::parse($share->expires_at)->format('M d, Y') }}
                        </p>
                        @endif
                    </div>
                </div>
                
                @if($share->personal_message)
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                    <p class="text-sm text-gray-700"><strong>Message:</strong> {{ $share->personal_message }}</p>
                </div>
                @endif
            </div>

            {{-- Documents Grid --}}
            @if($documents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($documents as $document)
                <div class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow">
                    <div class="mb-3">
                        @if($document->thumbnail_path)
                        <img src="{{ asset('storage/' . $document->thumbnail_path) }}" 
                            alt="Thumbnail" 
                            class="w-full h-32 object-cover rounded mb-2">
                        @else
                        <div class="w-full h-32 bg-gray-200 rounded flex items-center justify-center">
                            <i class="fas fa-file text-4xl text-gray-400"></i>
                        </div>
                        @endif
                    </div>
                    
                    <h3 class="font-semibold text-gray-900 mb-2">
                        {{ $document->document_name ?? ($document->documentType->name ?? 'Document') }}
                    </h3>
                    
                    <div class="space-y-1 text-sm text-gray-600 mb-3">
                        @if($document->document_number)
                        <p><i class="fas fa-hashtag mr-2"></i>{{ $document->document_number }}</p>
                        @endif
                        @if($document->documentType)
                        <p><i class="fas fa-tag mr-2"></i>{{ $document->documentType->name }}</p>
                        @endif
                        @if($document->issuing_authority)
                        <p><i class="fas fa-building mr-2"></i>{{ $document->issuing_authority }}</p>
                        @endif
                        @if($document->expiry_date)
                        <p><i class="fas fa-calendar-alt mr-2"></i>
                            Expires: {{ \Carbon\Carbon::parse($document->expiry_date)->format('M d, Y') }}
                        </p>
                        @endif
                    </div>
                    
                    @if($document->file_path)
                    <a href="{{ asset('storage/' . $document->file_path) }}" 
                        target="_blank" 
                        class="block w-full text-center bg-[#0053FF] text-white px-4 py-2 rounded-md hover:bg-[#0044DD] transition-colors">
                        <i class="fas fa-download mr-2"></i>Download
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-folder-open text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">No documents available</p>
            </div>
            @endif

            {{-- Footer --}}
            <div class="mt-8 text-center text-sm text-gray-600">
                <p>Powered by Yacht Workers Council</p>
            </div>
        </div>
    </div>
</body>
</html>
