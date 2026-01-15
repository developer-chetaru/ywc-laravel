<div>
    <main class="flex-1 flex flex-col bg-gray-100 p-4 sm:p-6">
        <div class="flex-1 overflow-hidden">
            <div class="p-4 bg-[#F5F6FA]">
                <div class="rounded-lg bg-white p-4 sm:p-6">
                    {{-- Header --}}
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Share Analytics</h1>
                        <p class="text-sm text-gray-600 mt-1">Track your document sharing performance</p>
                    </div>

                    @if($loading)
                    <div class="text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#0053FF]"></div>
                        <p class="mt-2 text-gray-600">Loading analytics...</p>
                    </div>
                    @else
                    {{-- Stats Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Shares</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $analytics['total_shares'] ?? 0 }}</p>
                                </div>
                                <i class="fas fa-share-alt text-3xl text-blue-500"></i>
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Active Shares</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $analytics['active_shares'] ?? 0 }}</p>
                                </div>
                                <i class="fas fa-check-circle text-3xl text-green-500"></i>
                            </div>
                        </div>

                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Views</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $analytics['total_views'] ?? 0 }}</p>
                                </div>
                                <i class="fas fa-eye text-3xl text-yellow-500"></i>
                            </div>
                        </div>

                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Downloads</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $analytics['total_downloads'] ?? 0 }}</p>
                                </div>
                                <i class="fas fa-download text-3xl text-purple-500"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Top Shared Documents --}}
                    @if(isset($analytics['top_shared_documents']) && $analytics['top_shared_documents']->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Most Shared Documents</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Times Shared</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($analytics['top_shared_documents'] as $doc)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $doc->document_name ?? 'Unnamed Document' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $doc->share_count }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    {{-- Recent Activity --}}
                    @if($recentActivity->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Activity</h2>
                        <div class="space-y-3">
                            @foreach($recentActivity as $activity)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    @if($activity->action === 'accessed')
                                    <i class="fas fa-eye text-blue-500"></i>
                                    @elseif($activity->action === 'downloaded')
                                    <i class="fas fa-download text-green-500"></i>
                                    @elseif($activity->action === 'created')
                                    <i class="fas fa-plus text-purple-500"></i>
                                    @elseif($activity->action === 'revoked')
                                    <i class="fas fa-ban text-red-500"></i>
                                    @else
                                    <i class="fas fa-circle text-gray-500"></i>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                @if($activity->ip_address)
                                <span class="text-xs text-gray-500">{{ $activity->ip_address }}</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
