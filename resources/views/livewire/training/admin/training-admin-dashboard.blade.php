<div>
    <main class="flex-1">
        <div class="w-full bg-white p-5 rounded-md pb-10">
            <h2 class="text-[#0053FF] text-[30px] font-semibold mb-6">Training & Resources - Admin Dashboard</h2>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="text-sm text-blue-600 font-semibold">Total Certifications</div>
                    <div class="text-2xl font-bold text-blue-700">{{ $stats['total_certifications'] }}</div>
                    <div class="text-xs text-blue-600 mt-1">{{ $stats['active_certifications'] }} active</div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="text-sm text-green-600 font-semibold">Total Providers</div>
                    <div class="text-2xl font-bold text-green-700">{{ $stats['total_providers'] }}</div>
                    <div class="text-xs text-green-600 mt-1">{{ $stats['active_providers'] }} active</div>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="text-sm text-purple-600 font-semibold">Total Courses</div>
                    <div class="text-2xl font-bold text-purple-700">{{ $stats['total_courses'] }}</div>
                    <div class="text-xs text-purple-600 mt-1">{{ $stats['active_courses'] }} active</div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="text-sm text-yellow-600 font-semibold">Pending Approvals</div>
                    <div class="text-2xl font-bold text-yellow-700">{{ $stats['pending_approvals'] + $stats['pending_course_approvals'] }}</div>
                    <div class="text-xs text-yellow-600 mt-1">Requires attention</div>
                </div>
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                    <div class="text-sm text-indigo-600 font-semibold">Total Reviews</div>
                    <div class="text-2xl font-bold text-indigo-700">{{ $stats['total_reviews'] }}</div>
                    <div class="text-xs text-indigo-600 mt-1">{{ $stats['pending_reviews'] }} pending</div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="text-sm text-red-600 font-semibold">User Certifications</div>
                    <div class="text-2xl font-bold text-red-700">{{ $stats['total_user_certifications'] }}</div>
                    <div class="text-xs text-red-600 mt-1">{{ $stats['expiring_soon'] }} expiring soon</div>
                </div>
                <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                    <div class="text-sm text-teal-600 font-semibold">Total Bookings</div>
                    <div class="text-2xl font-bold text-teal-700">{{ $stats['total_bookings'] }}</div>
                </div>
                <div class="bg-pink-50 border border-pink-200 rounded-lg p-4">
                    <div class="text-sm text-pink-600 font-semibold">Total Views</div>
                    <div class="text-2xl font-bold text-pink-700">{{ number_format($stats['total_views']) }}</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-3">Quick Actions</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('training.admin.certifications') }}" 
                       class="px-4 py-2 bg-[#0053FF] text-white rounded-lg hover:bg-blue-700">
                        Manage Certifications
                    </a>
                    <a href="{{ route('training.admin.providers') }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Manage Providers
                    </a>
                    <a href="{{ route('training.admin.courses') }}" 
                       class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Manage Courses
                    </a>
                    <a href="{{ route('training.admin.reviews') }}" 
                       class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        Manage Reviews
                    </a>
                </div>
            </div>

            <!-- Pending Approvals -->
            @if($pendingApprovals->count() > 0 || $pendingCourseApprovals->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3 text-yellow-600">⚠️ Pending Approvals</h3>
                    
                    @if($pendingApprovals->count() > 0)
                        <div class="mb-4">
                            <h4 class="font-semibold mb-2">Certifications Pending Approval ({{ $pendingApprovals->count() }})</h4>
                            <div class="space-y-2">
                                @foreach($pendingApprovals as $cert)
                                    <div class="border border-yellow-200 rounded p-3 bg-yellow-50">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <div class="font-semibold">{{ $cert->name }}</div>
                                                <div class="text-sm text-gray-600">{{ $cert->category->name }}</div>
                                            </div>
                                            <a href="{{ route('training.admin.certifications') }}?search={{ $cert->name }}" 
                                               class="px-3 py-1 bg-yellow-600 text-white rounded text-sm hover:bg-yellow-700">
                                                Review
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($pendingCourseApprovals->count() > 0)
                        <div>
                            <h4 class="font-semibold mb-2">Courses Pending Approval ({{ $pendingCourseApprovals->count() }})</h4>
                            <div class="space-y-2">
                                @foreach($pendingCourseApprovals as $course)
                                    <div class="border border-yellow-200 rounded p-3 bg-yellow-50">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <div class="font-semibold">{{ $course->certification->name }}</div>
                                                <div class="text-sm text-gray-600">{{ $course->provider->name }}</div>
                                            </div>
                                            <a href="{{ route('training.admin.courses') }}?search={{ $course->certification->name }}" 
                                               class="px-3 py-1 bg-yellow-600 text-white rounded text-sm hover:bg-yellow-700">
                                                Review
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Recent Activity -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-3">Recent Certifications</h3>
                    <div class="space-y-2">
                        @foreach($recentCertifications as $cert)
                            <div class="border rounded p-3">
                                <div class="font-semibold">{{ $cert->name }}</div>
                                <div class="text-sm text-gray-600">{{ $cert->category->name }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Created: {{ $cert->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-3">Recent Providers</h3>
                    <div class="space-y-2">
                        @foreach($recentProviders as $provider)
                            <div class="border rounded p-3">
                                <div class="font-semibold">{{ $provider->name }}</div>
                                <div class="text-sm text-gray-600">
                                    {{ $provider->activeCourses()->count() }} active courses
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Created: {{ $provider->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Top Courses -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold mb-3">Top Courses by Views</h3>
                <div class="space-y-2">
                    @foreach($topCourses as $course)
                        <div class="border rounded p-3">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="font-semibold">{{ $course->certification->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $course->provider->name }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold">{{ number_format($course->view_count) }} views</div>
                                    <div class="text-sm text-gray-600">{{ $course->review_count }} reviews</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
</div>
