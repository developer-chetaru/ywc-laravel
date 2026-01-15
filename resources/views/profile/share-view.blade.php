<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->first_name }} {{ $user->last_name }} - Shared Profile - YWC</title>
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
                    <div class="flex items-center gap-4">
                        <img src="{{ $user->profile_photo_path ? asset('storage/'.$user->profile_photo_path) : 'https://ui-avatars.com/api/?name='.$user->first_name.'+'.$user->last_name }}" 
                             class="w-16 h-16 rounded-full object-cover" alt="Profile">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</h1>
                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Views: {{ $share->view_count ?? $share->access_count ?? 0 }}</p>
                        <p class="text-sm text-gray-600">Downloads: {{ $share->download_count ?? 0 }}</p>
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

            {{-- Personal Information --}}
            @if($share->hasSection('personal_info'))
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="font-semibold">{{ $user->first_name }} {{ $user->last_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-semibold">{{ $user->email }}</p>
                    </div>
                    @if($user->phone)
                    <div>
                        <p class="text-sm text-gray-600">Phone</p>
                        <p class="font-semibold">{{ $user->phone }}</p>
                    </div>
                    @endif
                    @if($user->nationality)
                    <div>
                        <p class="text-sm text-gray-600">Nationality</p>
                        <p class="font-semibold">{{ $user->nationality }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Documents --}}
            @if($share->hasSection('documents') && isset($documents) && $documents->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Documents</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($documents as $document)
                    <div class="border border-gray-200 rounded-lg p-4">
                        @if($document->thumbnail_path)
                        <img src="{{ asset('storage/' . $document->thumbnail_path) }}" 
                            alt="Thumbnail" 
                            class="w-full h-24 object-cover rounded mb-2">
                        @endif
                        <h3 class="font-semibold text-sm mb-1">
                            {{ $document->document_name ?? ($document->documentType->name ?? 'Document') }}
                        </h3>
                        @if($document->expiry_date)
                        <p class="text-xs text-gray-600">
                            Expires: {{ \Carbon\Carbon::parse($document->expiry_date)->format('M d, Y') }}
                        </p>
                        @endif
                        @if($document->file_path)
                        <a href="{{ asset('storage/' . $document->file_path) }}" 
                            target="_blank" 
                            class="mt-2 block text-center text-xs bg-[#0053FF] text-white px-3 py-1 rounded hover:bg-[#0044DD]">
                            Download
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Career History --}}
            @if($share->hasSection('career_history') && isset($careerEntries) && $careerEntries->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Career History</h2>
                <div class="space-y-4">
                    @foreach($careerEntries as $entry)
                    <div class="border-l-4 border-[#0053FF] pl-4 py-2">
                        <h3 class="font-semibold text-gray-900">{{ $entry->vessel_name ?? 'Vessel' }}</h3>
                        <p class="text-sm text-gray-600">{{ $entry->position ?? 'Position' }}</p>
                        <p class="text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($entry->start_date)->format('M Y') }} - 
                            @if($entry->end_date)
                            {{ \Carbon\Carbon::parse($entry->end_date)->format('M Y') }}
                            @else
                            Present
                            @endif
                        </p>
                        @if($entry->vessel_type)
                        <p class="text-xs text-gray-500">Type: {{ $entry->vessel_type }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Download All as ZIP --}}
            @if($share->hasSection('documents') && isset($documents) && $documents->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-2">Download All Documents</h2>
                        <p class="text-sm text-gray-600">Download all shared documents as a ZIP file</p>
                    </div>
                    <form action="{{ route('profile.share.download', $share->share_token) }}" method="POST">
                        @csrf
                        <button type="submit" 
                            class="bg-[#0053FF] text-white px-6 py-3 rounded-md hover:bg-[#0044DD] transition-colors font-medium">
                            <i class="fas fa-download mr-2"></i>Download All as ZIP
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Connect on YWC CTA --}}
            <div class="bg-gradient-to-r from-[#0053FF] to-[#0044DD] rounded-lg shadow-md p-8 mb-6 text-white text-center">
                <h2 class="text-2xl font-bold mb-2">Connect on Yacht Workers Council</h2>
                <p class="mb-4">Join thousands of yacht crew members managing their careers with YWC</p>
                <a href="{{ route('register') }}" 
                    class="inline-block bg-white text-[#0053FF] px-8 py-3 rounded-md hover:bg-gray-100 transition-colors font-semibold">
                    Join YWC Today
                </a>
            </div>

            {{-- Footer --}}
            <div class="mt-8 text-center text-sm text-gray-600">
                <p>Powered by Yacht Workers Council</p>
            </div>
        </div>
    </div>
</body>
</html>
