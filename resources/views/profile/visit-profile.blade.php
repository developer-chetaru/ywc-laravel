<!doctype html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>{{ $user->name }} - Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .icon-gradient {
            background: linear-gradient(to bottom, #4f46e5, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .text-gradient {
            background-image: linear-gradient(to right, #4F46E5, #8B5CF6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .start-now-icon {
            background-color: #ff6347;
        }
    </style>
</head>
<body class="h-full bg-gray-100 flex items-start justify-center py-8 px-4 font-sans">

    <div class="w-full max-w-sm">

        <!-- User Info -->
        <div class="flex flex-col items-center mb-6">
            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-5xl font-bold">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h1 class="text-xl font-semibold mt-3">{{ $user->name }}</h1>
            <p class="text-gray-500 text-sm">{{ $user->email }}</p>
        </div>

        <!-- Documents -->
        @foreach($user->documents as $doc)
            <div class="bg-white rounded-lg shadow p-4 mb-4 cursor-pointer hover:bg-gray-50 transition"
                 onclick="openDocumentPopup('{{ $doc->id }}', '{{ $doc->type }}', '{{ \Carbon\Carbon::parse($doc->created_at)->diffForHumans() }}', '{{ $doc->expiry_date ? \Carbon\Carbon::parse($doc->expiry_date)->format('d M Y') : '' }}')">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium text-gray-800">{{ $doc->type }}</p>
                        <p class="text-sm text-gray-500">Added: {{ \Carbon\Carbon::parse($doc->created_at)->diffForHumans() }}</p>
                        @if($doc->expiry_date)
                            <p class="text-sm text-red-500">Expires: {{ \Carbon\Carbon::parse($doc->expiry_date)->format('d M Y') }}</p>
                        @endif
                    </div>
                    <button class="w-8 h-8 rounded-full bg-red-100 text-red-500 flex items-center justify-center hover:bg-red-200 transition"
                            onclick="event.stopPropagation(); openDeletePopup('{{ $doc->id }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endforeach

        
        <!-- More Options -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
                <h2 class="font-bold text-lg text-gray-800">Need more?</h2>
                <p class="text-sm text-gray-500">Supercharge your data collection and management!</p>
            </div>
            <div class="divide-y divide-gray-200">
                <!-- Example option block -->
                <a href="#" class="flex items-center p-4 hover:bg-gray-50 transition">
                    <span class="p-2 mr-3 rounded-full bg-orange-100 text-orange-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="font-medium text-gray-800">Request more from {{ $user->name }}</p>
                        <p class="text-sm text-gray-500">Streamline the collection of data and docs with Workflows</p>
                    </div>
                </a>
                <a href="#" class="flex items-center p-4 hover:bg-gray-50 transition">
                    <span class="p-2 mr-3 rounded-full bg-blue-100 text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM10 11a4 4 0 00-8 0v3h8v-3z" />
                        </svg>
                    </span>
                    <div>
                        <p class="font-medium text-gray-800">Crew portal</p>
                        <p class="text-sm text-gray-500">Never miss an update with our crew interface and notifications</p>
                    </div>
                </a>

                <a href="#" class="flex items-center p-4 hover:bg-gray-50 transition">
                    <span class="p-2 mr-3 rounded-full bg-green-100 text-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <div>
                        <p class="font-medium text-gray-800">Verify documents</p>
                        <p class="text-sm text-gray-500">Automate reaching out to issuers for verification</p>
                    </div>
                </a>

                <a href="#" class="flex items-center p-4 hover:bg-gray-50 transition">
                    <span class="p-2 mr-3 rounded-full bg-purple-100 text-purple-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M15 8a3 3 0 10-2.977-2.977l-1.287 1.287a2 2 0 00-.595.595L9.363 8.358A2 2 0 008 11v3h1a4 4 0 11-8 0v-3h1a2 2 0 00-2 2v3h-1a4 4 0 11-8 0v-3h1a2 2 0 00-2-2v-3h-1a4 4 0 11-8 0v-3h1a2 2 0 00-2-2v-3h-1a4 4 0 11-8 0v-3z" />
                        </svg>
                    </span>
                    <div>
                        <p class="font-medium text-gray-800">Sharing and collaboration</p>
                        <p class="text-sm text-gray-500">Generate documents and share them with groups of crew</p>
                    </div>
                </a>

                <a href="#" class="flex items-center p-4 hover:bg-gray-50 transition">
                    <span class="p-2 mr-3 rounded-full start-now-icon text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l4.421-1.105 4.092 4.092a1 1 0 001.414-1.414l-4.092-4.092 1.105-4.421a1 1 0 00-.978-1.22l-7-14z" />
                        </svg>
                    </span>
                    <div>
                        <p class="font-medium text-gray-800">Start now for FREE</p>
                        <p class="text-sm text-gray-500">All this and more with a Starter account (no card details required)</p>
                    </div>
                </a>
            </div>
        </div>

    </div>

    

    <script>
        function openDocumentPopup(docId, docType, addedAgo, expiryDate) {
            const popup = document.getElementById('documentPopup');
            const content = document.getElementById('documentContent');
            
            // Fill the popup content dynamically
            content.innerHTML = `
                <p><strong>ID:</strong> ${docId}</p>
                <p><strong>Type:</strong> ${docType}</p>
                <p><strong>Added:</strong> ${addedAgo}</p>
                ${expiryDate ? `<p><strong>Expires:</strong> ${expiryDate}</p>` : ''}
            `;
            
            popup.classList.remove('hidden');
        }

        function closeDocumentPopup() {
            const popup = document.getElementById('documentPopup');
            popup.classList.add('hidden');
        }

        function openDeletePopup(docId) {
            alert('Delete document with ID: ' + docId);
            // Implement deletion logic here
        }
    </script>

</body>
</html>