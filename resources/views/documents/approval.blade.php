<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document Approval</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Login Section (shown when not authenticated) --}}
            <div id="loginSection" class="max-w-md mx-auto mt-20">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Login to Approve Documents</h2>
                    
                    <div id="loginError" class="hidden bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4"></div>
                    <div id="loginSuccess" class="hidden bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4"></div>

                    <form id="loginForm">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input 
                                type="email" 
                                id="email"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter your email"
                                required
                            >
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input 
                                type="password" 
                                id="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter your password"
                                required
                            >
                        </div>

                        <button 
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
                        >
                            Login
                        </button>
                    </form>

                    <p class="text-sm text-gray-600 mt-4 text-center">
                        Or access via token: <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ url('/documents/approval?token=YOUR_TOKEN') }}</code>
                    </p>
                </div>
            </div>

            {{-- Document List Section (shown when authenticated) --}}
            <div id="documentsSection" class="hidden">
                {{-- Header --}}
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Document Approval</h1>
                            <p class="text-gray-600 mt-1">Review and approve or reject pending documents</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-blue-600" id="totalCount">0</div>
                            <div class="text-sm text-gray-500">Pending Documents</div>
                        </div>
                    </div>

                    {{-- Search and Filter --}}
                    <div class="flex flex-col sm:flex-row gap-4 mt-4">
                        <div class="flex-1">
                            <input 
                                type="text" 
                                id="searchInput"
                                placeholder="Search by document name, number, or owner name..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <div class="sm:w-48">
                            <select 
                                id="filterType"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="all">All Types</option>
                                <option value="passport">Passport</option>
                                <option value="idvisa">ID/Visa</option>
                                <option value="certificate">Certificate</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Success Message --}}
                <div id="successMessage" class="hidden bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
                    <span id="successText"></span>
                    <button onclick="document.getElementById('successMessage').classList.add('hidden')" class="text-green-600 hover:text-green-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Documents Grid --}}
                <div id="documentsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Documents will be loaded here -->
                </div>

                {{-- Pagination --}}
                <div id="pagination" class="mt-6 hidden"></div>

                {{-- Empty State --}}
                <div id="emptyState" class="hidden bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Pending Documents</h3>
                    <p class="text-gray-600">All documents have been reviewed. Great job! 🎉</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Approval/Rejection Modal --}}
    <div id="approvalModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6 z-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-2xl font-bold text-gray-900" id="modalTitle">Approve Document</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <p class="text-gray-700 mb-2">
                        <span class="font-semibold">Document:</span> 
                        <span id="modalDocumentName"></span>
                    </p>
                    <p class="text-gray-700 mb-2">
                        <span class="font-semibold">Owner:</span> 
                        <span id="modalOwnerName"></span>
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Notes (Optional)
                    </label>
                    <textarea 
                        id="modalNotes"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Add any notes about your decision..."
                    ></textarea>
                </div>

                <div class="flex gap-3 justify-end">
                    <button 
                        onclick="closeModal()"
                        class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        id="confirmButton"
                        onclick="confirmAction()"
                        class="px-6 py-2 text-white rounded-lg font-medium transition-colors flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span id="confirmButtonText">Confirm</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let currentAction = '';
        let currentDocumentId = null;
        let authToken = null;

        // Helper functions (define first)
        function showLogin() {
            document.getElementById('loginSection').classList.remove('hidden');
            document.getElementById('documentsSection').classList.add('hidden');
        }

        function showDocuments() {
            console.log('showDocuments called, authToken:', authToken ? 'present' : 'missing');
            document.getElementById('loginSection').classList.add('hidden');
            document.getElementById('documentsSection').classList.remove('hidden');
            if (authToken) {
                loadDocuments();
            } else {
                console.error('Cannot load documents: no auth token');
                showError('Authentication required. Please login again.');
                showLogin();
            }
        }

        // Initialize: Always show login section first
        showLogin();

        // Check for token in URL first
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        if (token) {
            authenticateWithToken(token);
        } else {
            // Check if already authenticated via localStorage
            const storedToken = localStorage.getItem('approval_auth_token');
            if (storedToken) {
                authToken = storedToken;
                // Verify token is still valid
                verifyToken();
            }
        }

        // Login form handler
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok && data.status) {
                    authToken = data.data?.token || data.token;
                    if (authToken) {
                        localStorage.setItem('approval_auth_token', authToken);
                        console.log('Login successful, token saved');
                        showDocuments();
                    } else {
                        console.error('No token in response:', data);
                        showError('Login successful but no token received');
                    }
                } else {
                    console.error('Login failed:', data);
                    showError(data.message || 'Invalid credentials');
                }
            } catch (error) {
                showError('Login failed. Please try again.');
            }
        });

        async function verifyToken() {
            if (!authToken) {
                console.log('No token to verify');
                return;
            }
            
            try {
                const response = await fetch('/api/user', {
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json',
                    }
                });

                if (response.ok) {
                    console.log('Token verified, showing documents');
                    showDocuments();
                } else {
                    console.log('Token invalid, showing login');
                    // Token invalid, clear it and show login
                    localStorage.removeItem('approval_auth_token');
                    authToken = null;
                }
            } catch (error) {
                console.error('Token verification error:', error);
                // Token invalid, clear it and show login
                localStorage.removeItem('approval_auth_token');
                authToken = null;
            }
        }

        async function authenticateWithToken(token) {
            try {
                const response = await fetch('/api/user', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                    }
                });

                if (response.ok) {
                    authToken = token;
                    localStorage.setItem('approval_auth_token', authToken);
                    showDocuments();
                } else {
                    showError('Invalid token');
                }
            } catch (error) {
                showError('Token authentication failed');
            }
        }

        function showError(message) {
            const errorDiv = document.getElementById('loginError');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            setTimeout(() => errorDiv.classList.add('hidden'), 5000);
        }

        async function loadDocuments(page = 1) {
            currentPage = page;
            const search = document.getElementById('searchInput')?.value || '';
            const filterType = document.getElementById('filterType')?.value || 'all';

            try {
                const params = new URLSearchParams({
                    page: page,
                    search: search,
                    filter_type: filterType,
                    status: 'pending'
                });

                const response = await fetch(`/api/documents/approval?${params}`, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json',
                    }
                });

                if (response.status === 401) {
                    // Token expired, show login
                    localStorage.removeItem('approval_auth_token');
                    authToken = null;
                    showLogin();
                    return;
                }

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('API Error:', response.status, errorText);
                    showError('Failed to load documents. Please try logging in again.');
                    showLogin();
                    return;
                }

                const data = await response.json();
                console.log('Documents response:', data);
                
                if (data.success && data.documents) {
                    const documentsList = data.documents.data || data.documents || [];
                    renderDocuments(documentsList);
                    document.getElementById('totalCount').textContent = data.documents.total || documentsList.length || 0;
                    if (data.documents.last_page) {
                        renderPagination(data.documents);
                    }
                } else {
                    console.error('Invalid response format:', data);
                    showError(data.message || 'Failed to load documents');
                }
            } catch (error) {
                console.error('Error loading documents:', error);
                showError('Failed to load documents: ' + error.message);
            }
        }

        function renderDocuments(documents) {
            console.log('renderDocuments called with:', documents.length, 'documents');
            const grid = document.getElementById('documentsGrid');
            const emptyState = document.getElementById('emptyState');

            if (!documents || documents.length === 0) {
                console.log('No documents to render, showing empty state');
                grid.classList.add('hidden');
                emptyState.classList.remove('hidden');
                return;
            }

            console.log('Rendering', documents.length, 'documents');
            grid.classList.remove('hidden');
            emptyState.classList.add('hidden');

            grid.innerHTML = documents.map(doc => `
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                    <div class="bg-gray-100 h-48 flex items-center justify-center relative">
                        ${getDocumentPreview(doc)}
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-900 mb-2 truncate">
                            ${doc.document_name || doc.document_type?.name || 'Unnamed Document'}
                        </h3>
                        <div class="space-y-2 text-sm text-gray-600 mb-4">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="font-medium">Owner:</span>
                                <span class="ml-1">${doc.user?.first_name || ''} ${doc.user?.last_name || ''}</span>
                            </div>
                            ${doc.document_number ? `
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span class="font-medium">Number:</span>
                                <span class="ml-1">${doc.document_number}</span>
                            </div>
                            ` : ''}
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span class="font-medium">Type:</span>
                                <span class="ml-1 capitalize">${doc.type || 'N/A'}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-medium">Uploaded:</span>
                                <span class="ml-1">${new Date(doc.created_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-4">
                            <button 
                                onclick="openModal(${doc.id}, 'approve', '${escapeHtml(doc.document_name || 'Document')}', '${escapeHtml((doc.user?.first_name || '') + ' ' + (doc.user?.last_name || ''))}')"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Approve
                            </button>
                            <button 
                                onclick="openModal(${doc.id}, 'reject', '${escapeHtml(doc.document_name || 'Document')}', '${escapeHtml((doc.user?.first_name || '') + ' ' + (doc.user?.last_name || ''))}')"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Reject
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function getDocumentPreview(doc) {
            if (!doc.file_path || doc.file_path === 'null' || doc.file_path === null) {
                return `
                    <div class="text-center text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm">No Preview</p>
                    </div>
                `;
            }

            const extension = doc.file_path.split('.').pop()?.toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
                return `<img src="${doc.file_path}" alt="${doc.document_name}" class="max-h-full max-w-full object-contain cursor-pointer" onclick="window.open('${doc.file_path}', '_blank')">`;
            } else if (extension === 'pdf') {
                return `
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto text-red-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 18h12V6h-4V2H4v16zm-2 1V0h12l4 4v16H2v-1z"/>
                        </svg>
                        <a href="${doc.file_path}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View PDF</a>
                    </div>
                `;
            }

            return `
                <div class="text-center text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm">No Preview</p>
                </div>
            `;
        }

        function renderPagination(pagination) {
            const paginationDiv = document.getElementById('pagination');
            if (!pagination || pagination.last_page <= 1) {
                paginationDiv.classList.add('hidden');
                return;
            }

            paginationDiv.classList.remove('hidden');
            let html = '<div class="flex justify-center gap-2">';
            
            if (pagination.current_page > 1) {
                html += `<button onclick="loadDocuments(${pagination.current_page - 1})" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Previous</button>`;
            }

            for (let i = 1; i <= pagination.last_page; i++) {
                if (i === pagination.current_page) {
                    html += `<button class="px-4 py-2 bg-blue-600 text-white rounded-lg">${i}</button>`;
                } else {
                    html += `<button onclick="loadDocuments(${i})" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">${i}</button>`;
                }
            }

            if (pagination.current_page < pagination.last_page) {
                html += `<button onclick="loadDocuments(${pagination.current_page + 1})" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</button>`;
            }

            html += '</div>';
            paginationDiv.innerHTML = html;
        }

        function openModal(documentId, action, documentName, ownerName) {
            currentDocumentId = documentId;
            currentAction = action;
            document.getElementById('modalTitle').textContent = action === 'approve' ? 'Approve Document' : 'Reject Document';
            document.getElementById('modalDocumentName').textContent = documentName;
            document.getElementById('modalOwnerName').textContent = ownerName;
            document.getElementById('modalNotes').value = '';
            
            const confirmButton = document.getElementById('confirmButton');
            const confirmText = document.getElementById('confirmButtonText');
            if (action === 'approve') {
                confirmButton.className = 'px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2';
                confirmText.textContent = 'Confirm Approval';
            } else {
                confirmButton.className = 'px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2';
                confirmText.textContent = 'Confirm Rejection';
            }

            document.getElementById('approvalModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('approvalModal').classList.add('hidden');
            currentDocumentId = null;
            currentAction = '';
        }

        async function confirmAction() {
            const notes = document.getElementById('modalNotes').value;

            try {
                const response = await fetch(`/api/documents/${currentDocumentId}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        action: currentAction,
                        notes: notes
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showSuccess(`Document ${currentAction}ed successfully!`);
                    closeModal();
                    loadDocuments(currentPage);
                } else {
                    showError(data.message || `Failed to ${currentAction} document`);
                }
            } catch (error) {
                showError(`Failed to ${currentAction} document`);
            }
        }

        function showSuccess(message) {
            const successDiv = document.getElementById('successMessage');
            document.getElementById('successText').textContent = message;
            successDiv.classList.remove('hidden');
            setTimeout(() => successDiv.classList.add('hidden'), 5000);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Search and filter handlers
        document.getElementById('searchInput')?.addEventListener('input', debounce(() => {
            loadDocuments(1);
        }, 300));

        document.getElementById('filterType')?.addEventListener('change', () => {
            loadDocuments(1);
        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    </script>
</body>
</html>
