<div class=" bg-gray-100 p-1">
    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-white">
                <tr class="text-sm font-medium text-gray-700">
                    <th class="px-4 py-6">User Name</th>
                    <th class="px-4 py-6">Email</th>
                    <th class="px-4 py-6 text-center">Total Document</th>
                    <th class="px-4 py-6 text-center">Pending</th>
                    <th class="px-4 py-6 text-center">Approved</th>
                    <th class="px-4 py-6 text-center">Rejected</th>
                    <th class="px-4 py-6 text-center">Action</th>
                </tr>
            </thead>

            <tbody class="text-sm text-gray-700">
                @forelse($users as $user)
                <tr class="odd:bg-gray-50 even:bg-white">
                    <td class="flex items-center gap-3 px-4 py-6">
                        <img src="{{ $user->profile_photo_path ? asset('storage/'.$user->profile_photo_path) : 'https://ui-avatars.com/api/?name='.$user->first_name.'+'.$user->last_name }}" 
                             class="w-8 h-8 rounded-full object-cover" alt="">
                        <span class="font-medium text-[#1B1B1B] truncate">{{ $user->first_name }} {{ $user->last_name }}</span>
                    </td>
                    <td class="px-4 py-6 text-[#616161] truncate">{{ $user->email }}</td>
                    <td class="px-4 py-6 text-center">{{ $user->documents_count }}</td>
                    <td class="px-4 py-6 text-center text-[#E07911]">{{ $user->pending_count }}</td>
                    <td class="px-4 py-6 text-center text-[#0C7B24]">{{ $user->approved_count }}</td>
                    <td class="px-4 py-6 text-center text-[#EB1C24]">{{ $user->rejected_count }}</td>
                    <td class="px-4 py-6 text-center text-[#616161]">
                        <a href="{{ route('career-history.show', $user->id) }}" 
                           class="text-[#616161]">View all</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-6 text-gray-500">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
    <div class="flex items-center justify-end gap-5 border-t border-gray-200 px-6 py-4 text-sm text-[#616161]">
        <p class="font-medium">
            Showing <span class="font-bold">{{ $users->firstItem() }}</span>
            to <span class="font-bold">{{ $users->lastItem() }}</span>
            of <span class="font-bold">{{ $users->total() }}</span> Results
        </p>

        <div class="flex items-center gap-2">
            {{-- Previous Page --}}
            @if ($users->onFirstPage())
                <button class="px-[15px] py-2 rounded-lg border bg-gray-200 text-gray-500 cursor-not-allowed">Prev</button>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="px-[15px] py-2 rounded-lg border bg-white hover:bg-blue-600 hover:text-white">
                    Prev
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                @if ($page == $users->currentPage())
                    <button class="px-[15px] py-2 rounded-lg border bg-blue-600 text-white">{{ $page }}</button>
                @else
                    <a href="{{ $url }}" class="px-[15px] py-2 rounded-lg border bg-white hover:bg-blue-600 hover:text-white">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            {{-- Next Page --}}
            @if ($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="px-[15px] py-2 rounded-lg border bg-white hover:bg-blue-600 hover:text-white">Next</a>
            @else
                <button class="px-[15px] py-2 rounded-lg border bg-gray-200 text-gray-500 cursor-not-allowed">Next</button>
            @endif
        </div>
    </div>
    @endif

</div>

