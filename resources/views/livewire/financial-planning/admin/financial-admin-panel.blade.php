<div class="py-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">⚙️ Financial Planning Admin Panel</h1>
                    <p class="text-gray-600 mt-1">Manage advisors, content, and consultations</p>
                </div>
                <a href="{{ route('financial.dashboard') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Dashboard
                </a>
            </div>

            {{-- Tabs --}}
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex space-x-8">
                    <button wire:click="$set('activeTab', 'overview')" 
                            class="px-4 py-2 border-b-2 {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                        Overview
                    </button>
                    <button wire:click="$set('activeTab', 'advisors')" 
                            class="px-4 py-2 border-b-2 {{ $activeTab === 'advisors' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                        Advisors
                    </button>
                    <button wire:click="$set('activeTab', 'content')" 
                            class="px-4 py-2 border-b-2 {{ $activeTab === 'content' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                        Educational Content
                    </button>
                    <button wire:click="$set('activeTab', 'stories')" 
                            class="px-4 py-2 border-b-2 {{ $activeTab === 'stories' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                        Success Stories
                    </button>
                    <button wire:click="$set('activeTab', 'consultations')" 
                            class="px-4 py-2 border-b-2 {{ $activeTab === 'consultations' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                        Consultations
                    </button>
                </nav>
            </div>

            {{-- Overview Tab --}}
            @if($activeTab === 'overview')
            <div class="grid md:grid-cols-4 gap-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total_users'] }}</div>
                    <div class="text-sm text-gray-600">Active Users</div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['total_consultations'] }}</div>
                    <div class="text-sm text-gray-600">Total Consultations</div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_consultations'] }}</div>
                    <div class="text-sm text-gray-600">Pending Consultations</div>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['total_advisors'] }}</div>
                    <div class="text-sm text-gray-600">Active Advisors</div>
                </div>
            </div>
            @endif

            {{-- Advisors Tab --}}
            @if($activeTab === 'advisors')
            <div class="mb-4">
                <button wire:click="openAdvisorForm" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    + Add Advisor
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Consultations</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($advisors as $advisor)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $advisor->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $advisor->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">⭐ {{ number_format($advisor->rating, 1) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $advisor->total_consultations }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($advisor->is_active)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button wire:click="openAdvisorForm({{ $advisor->id }})" 
                                        class="text-blue-600 hover:text-blue-900">Edit</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $advisors->links() }}</div>
            @endif

            {{-- Content Tab --}}
            @if($activeTab === 'content')
            <div class="mb-4">
                <button wire:click="openContentForm" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    + Add Content
                </button>
            </div>
            <div class="grid md:grid-cols-3 gap-4">
                @foreach($content as $item)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-semibold">{{ $item->title }}</h3>
                    <p class="text-sm text-gray-600">{{ ucfirst($item->type) }}</p>
                    <div class="mt-2">
                        @if($item->is_published)
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Published</span>
                        @else
                            <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">Draft</span>
                        @endif
                    </div>
                    <button wire:click="openContentForm({{ $item->id }})" 
                            class="mt-2 text-blue-600 text-sm">Edit</button>
                </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $content->links() }}</div>
            @endif

            {{-- Stories Tab --}}
            @if($activeTab === 'stories')
            <div class="mb-4">
                <button wire:click="openStoryForm" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    + Add Story
                </button>
            </div>
            <div class="grid md:grid-cols-3 gap-4">
                @foreach($stories as $story)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-semibold">{{ $story->name }}</h3>
                    <p class="text-sm text-gray-600">{{ Str::limit($story->story, 80) }}</p>
                    <div class="mt-2 flex gap-2">
                        @if($story->is_published)
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Published</span>
                        @else
                            <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">Draft</span>
                        @endif
                        @if($story->is_featured)
                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Featured</span>
                        @endif
                    </div>
                    <button wire:click="openStoryForm({{ $story->id }})" 
                            class="mt-2 text-blue-600 text-sm">Edit</button>
                </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $stories->links() }}</div>
            @endif

            {{-- Consultations Tab --}}
            @if($activeTab === 'consultations')
            @if(session('message'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-lg mb-4">
                    {{ session('message') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Advisor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scheduled</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($consultations as $consultation)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $consultation->user->first_name }} {{ $consultation->user->last_name }}</div>
                                <div class="text-sm text-gray-500">{{ $consultation->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $consultation->advisor->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($consultation->type) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>{{ $consultation->scheduled_at->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $consultation->scheduled_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full font-semibold
                                    {{ $consultation->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($consultation->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : 
                                       ($consultation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       'bg-red-100 text-red-800')) }}">
                                    {{ ucfirst($consultation->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if($consultation->status !== 'confirmed')
                                    <button wire:click="updateConsultationStatus({{ $consultation->id }}, 'confirmed')" 
                                            class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                                            onclick="return confirm('Confirm this consultation?')">
                                        ✓ Confirm
                                    </button>
                                    @endif
                                    @if($consultation->status !== 'completed' && $consultation->status !== 'cancelled')
                                    <button wire:click="updateConsultationStatus({{ $consultation->id }}, 'completed')" 
                                            class="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
                                            onclick="return confirm('Mark this consultation as completed?')">
                                        ✓ Complete
                                    </button>
                                    @endif
                                    @if($consultation->status !== 'cancelled')
                                    <button wire:click="updateConsultationStatus({{ $consultation->id }}, 'cancelled')" 
                                            class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
                                            onclick="return confirm('Cancel this consultation?')">
                                        ✗ Cancel
                                    </button>
                                    @endif
                                    @if($consultation->status === 'cancelled' || $consultation->status === 'pending')
                                    <button wire:click="updateConsultationStatus({{ $consultation->id }}, 'pending')" 
                                            class="px-3 py-1 text-xs bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors">
                                        ↻ Reset to Pending
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $consultations->links() }}</div>
            @endif
        </div>
    </div>
</div>

