<div>
    @role('super_admin')
    <!-- Example: choose one of the class sets -->
    <main class="flex-1 overflow-y-auto p-4">
        <div>
            <div class="h-[calc(100vh-100px)] bg-gray-100">
                
                <form action="" class="flex gap-[16px] mb-4">
                    {{-- 🔍 Search --}}
                    <div class="relative w-[39%]"> 
                        <input type="text" placeholder="Search by name, email, or ID" 
                            wire:model.live.debounce.300ms="search"
                            class="text-[#616161] placeholder-[#616161] w-full py-3 px-4 rounded-lg border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 text-sm !pl-[40px] font-medium !bg-[#ffffff]">
                        <button class="!absolute !left-3 !top-1/2 !transform !-translate-y-1/2" type="button">
                            <img src="https://console-ywc.nativeappdev.com/images/search.svg" alt="">
                        </button>
                    </div>

                    <div class="relative">
                        <select wire:model.live="statusFilter"
                            class="appearance-none text-[#616161] py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-blue-200 text-sm font-medium bg-white cursor-pointer px-3 pr-10 min-w-[130px]">
                            <option value="all">Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        <img class="absolute right-3 top-1/2 transform -translate-y-1/2 w-[10px] pointer-events-none"
                            src="{{ asset('images/down-arr.svg') }}" alt="">
                    </div>

                    {{-- 🔤 Sort by --}}
                    <div class="relative">
                        <select wire:model.live="sortOrder"
                            class="appearance-none status text-[#616161] py-3 rounded-lg border border-gray-200 focus:outline-none focus:border focus:!border-blue-200 text-sm font-medium !bg-[#ffffff] cursor-pointer pr-4 pl-9 min-w-[150px]">
                            <option value="asc">Sort by: A to Z</option>
                            <option value="desc">Sort by: Z to A</option>
                        </select>
                        <img class="!absolute !left-3 !top-1/2 !transform !-translate-y-1/2 w-[20px] cursor-pointer"
                            src="images/sorting-01.svg" alt="">
                    </div>
                    
                    <button type="button" wire:click="openAddModal" class="cursor-pointer bg-[#0053FF] flex gap-2 justify-center items-center px-5 py-2 rounded-md text-white text-sm leading-[0px] ml-auto">
                        <img class="h-[18px] w-[18px]" src="images/add-circle-white.svg" alt="">
                        Add New Role
                    </button>

                </form>

                <div>
                    @if (session()->has('success'))
                        <div id="success-alert" 
                            class="w-full bg-blue-500 text-white text-center py-2 rounded-md mb-4 animate-fadeInOut">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div id="error-alert" 
                            class="w-full bg-red-500 text-white text-center py-2 rounded-md mb-4 animate-fadeInOut">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="bg-white rounded-lg shadow-sm w-full overflow-hidden">
                        <table class="w-full text-left border-separate border-spacing-y-0">
                            <thead>
                                <tr class="text-sm text-white-500 border-b">

                                    <th class="px-4 py-6 font-medium text-[#020202] w-[62px]">#</th>
                                    <th class="px-4 py-6 font-medium text-[#020202]">Role Name</th>
                                    <th class="px-4 py-6 font-medium text-[#020202]">Linked Users</th>
                                    <th class="px-4 py-6 font-medium text-[#020202] text-center">Status</th>
                                    <th class="px-4 py-6 font-medium text-[#020202] text-center">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="text-gray-700 text-sm">
                                @forelse($roles as $role)
                                <tr class="bg-gray-50 hover:bg-gray-100">
                                    <td class="text-[#1B1B1B] px-4 py-6">{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}</td>
                                    <td class="px-4 py-6 text-[#616161]">{{ $role->name }}</td>
                                    <td class="px-4 py-3 text-[#0053FF] font-medium cursor-pointer hover:underline"
                                        onclick='openProfileSidebar("{{ $role->name }}", @json($role->users->map(fn($u)=>[
                                            "name"=>$u->name,
                                            "email"=>$u->email,
                                             "image" => $u->profile_photo_path ? asset("storage/".$u->profile_photo_path) : "https://via.placeholder.com/40"
                                        ])))'>
                                        {{ $role->users_count }} {{ Str::plural('User', $role->users_count) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" wire:click="toggleStatus({{ $role->id }})" {{ $role->status === 'Active' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 border-gray-300 rounded cursor-pointer focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-6 text-[#0C7B24] text-center">
                                    <div class="justify-center flex gap-2 ">
                                        <button class="cursor-pointer" wire:click="edit({{ $role->id }})">
                                            <img class="w-[37px] h-[37px]" src="images/edit.svg" alt="">
                                        </button>
                                        <button class="cursor-pointer" wire:click="delete({{ $role->id }})">
                                            <img class="w-[37px] h-[37px]" src="images/del.svg" alt="">
                                        </button>
                                    </div>
                                    </td>
                                    
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="border px-4 py-3 text-center text-gray-500">
                                        No roles found.
                                    </td>
                                </tr>
                                @endforelse                                
                            </tbody>
                        </table>

                    </div>

                    <div class="flex items-center justify-between border-t border-gray-200 px-6 py-4 text-sm text-[#616161]">
                        <p class="font-medium">
                            Showing 
                            <span class="font-bold">{{ $roles->firstItem() ?? 0 }}</span> 
                            to 
                            <span class="font-bold">{{ $roles->lastItem() ?? 0 }}</span> 
                            of 
                            <span class="font-bold">{{ $roles->total() }}</span> Results
                        </p>

                        <div class="flex items-center gap-2">
                            {{-- Previous Page --}}
                            @if ($roles->onFirstPage())
                                <button disabled class="flex justify-center items-center rounded-lg border bg-gray-100 text-gray-400 h-[37px] w-[37px]">
                                    <img src="images/right-arr.svg" class="rotate-180 opacity-40" alt="">
                                </button>
                            @else
                                <button wire:click="previousPage" class="flex justify-center items-center rounded-lg border bg-white hover:bg-blue-600 hover:text-white h-[37px] w-[37px]">
                                    <img src="images/right-arr.svg" class="rotate-180" alt="">
                                </button>
                            @endif

                            {{-- Page Numbers --}}
                            @foreach ($roles->getUrlRange(1, $roles->lastPage()) as $page => $url)
                                @if ($page == $roles->currentPage())
                                    <button class="px-[15px] py-2 rounded-lg border bg-blue-600 text-white">{{ $page }}</button>
                                @else
                                    <button wire:click="gotoPage({{ $page }})"
                                        class="px-[15px] py-2 rounded-lg border bg-white hover:bg-blue-600 hover:text-white">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach

                            {{-- Next Page --}}
                            @if ($roles->hasMorePages())
                                <button wire:click="nextPage" class="flex justify-center items-center rounded-lg border bg-white hover:bg-blue-600 hover:text-white h-[37px] w-[37px]">
                                    <img src="images/right-arr.svg" alt="">
                                </button>
                            @else
                                <button disabled class="flex justify-center items-center rounded-lg border bg-gray-100 text-gray-400 h-[37px] w-[37px]">
                                    <img src="images/right-arr.svg" class="opacity-40" alt="">
                                </button>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </main>
    @if($showModal)
<div class="fixed inset-0 bg-black/30 flex justify-center items-center z-50 transition-opacity duration-300 animate-fadeIn">
    <div class="bg-white shadow-xl rounded-xl w-full max-w-2xl p-8 relative animate-popupShow">

        <button wire:click="closeModal"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>

        <h2 class="text-xl font-semibold text-gray-800 mb-6">
            {{ $isEditMode ? 'Edit Role' : 'Add New Role' }}
        </h2>

        {{-- Form --}}
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Role Name <span class="text-red-500">*</span>
                </label>
                <input type="hidden" wire:model="guard_name">
                <input type="text" wire:model="name"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 text-sm">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Status <span class="text-red-500">*</span>
                </label>
                <select wire:model="status"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 text-sm">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end mt-6">
                @if($isEditMode)
                    <button wire:click="update"
                        class="bg-[#0053FF] text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-blue-600 transition">
                        Update Role
                    </button>
                @else
                    <button wire:click="save"
                        class="bg-[#0053FF] text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-blue-600 transition">
                        Create Role
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

    {{-- ✅ Right Sidebar Popup --}}
    <div id="profileOverlay" class="fixed inset-0 bg-black bg-opacity-60 hidden z-40 transition-opacity duration-300"></div>

    <div id="profileSidebar"
        class="fixed right-0 top-0 h-screen w-[36%] bg-white shadow-2xl p-7 pt-5 py-10 transform translate-x-full transition-transform duration-500 z-50 hidden">

        <!-- Close Button -->
        <button id="closeProfileSidebar" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
            <img class="w-[30px] h-[30px]" src="{{ asset('images/cross.svg') }}" alt="Close">
        </button>

        <!-- Title -->
        <h3 class="mb-5 text-[#0053FF] text-xl">
            Linked Users to <strong id="profileRoleTitle">Role</strong>
        </h3>

        <!-- 🔍 Search -->
        <form class="mb-4 w-full">
            <div class="relative">
                <input type="text" id="userSearch" placeholder="Search by name, email, or ID"
                    class="text-[#616161] placeholder-[#616161] w-full py-3 px-4 rounded-lg border border-gray-200 
                        focus:outline-none focus:border-blue-200 text-sm !pl-[40px] font-medium !bg-[#ffffff]">
                <button type="button"
                    class="!absolute !left-3 !top-1/2 !transform !-translate-y-1/2 pointer-events-none">
                    <img src="https://console-ywc.nativeappdev.com/images/search.svg" alt="search">
                </button>
            </div>
        </form>

        <div class="overflow-hidden border border-gray-200 rounded-lg h-[calc(100vh-160px)] flex flex-col">
            <!-- ✅ Fixed Header -->
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#F8F9FA] border-b border-gray-200 sticky top-0 z-10">
                <tr>
                    <th class="py-4 px-4 font-semibold text-[#020202] text-sm">User Name</th>
                    <th class="py-4 px-4 font-semibold text-[#020202] text-sm">Email</th>
                </tr>
                </thead>
            </table>

            <!-- ✅ Scrollable Body Section -->
            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-left border-collapse">
                <tbody id="linkedUsersBody">
                    <tr class="text-center">
                    <td colspan="2" class="py-6 text-gray-500">No users linked</td>
                    </tr>
                </tbody>
                </table>
            </div>
        </div>
    </div>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}

@keyframes popupShow {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}

.animate-popupShow {
    animation: popupShow 0.3s ease-out;
}

/* ✅ Alerts show, stay 2s, then fade smoothly */
.animate-fadeInOut {
    animation: fadeIn 0.3s ease-out,
               fadeOut 0.6s ease-in 2s;
}

@keyframes fadeIn { 
    from { opacity: 0; } 
    to { opacity: 1; } 
}

@keyframes fadeOut { 
    from { opacity: 1; } 
    to { opacity: 0; } 
}

@keyframes popupShow { 
    from { opacity: 0; transform: scale(0.95); } 
    to { opacity: 1; transform: scale(1); } 
}

.animate-fadeIn { 
    animation: fadeIn 0.3s ease-out; 
}

.animate-popupShow { 
    animation: popupShow 0.3s ease-out; 
}

/* ✅ Alerts stay 2s then fade out smoothly */
.animate-fadeInOut {
    animation: fadeIn 0.3s ease-out, fadeOut 0.6s ease-in 3s;
}

/* ✅ Softer transparent black overlay for sidebar background */
#profileOverlay {
    background: rgba(0, 0, 0, 0.45); /* semi-transparent black */
    transition: opacity 0.4s ease;
}

</style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
document.addEventListener('DOMContentLoaded', () => {
    // Livewire + SweetAlert (same as you had)
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('confirm-delete', (data) => {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action will permanently delete the role.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('confirmDelete', { id: data.id });
                }
            });
        });
    });

    // Success / error auto-hide (keeps your behavior)
    const successAlert = document.getElementById('success-alert');
    const errorAlert = document.getElementById('error-alert');

    if (successAlert) {
        setTimeout(() => {
            successAlert.style.transition = 'opacity 0.6s ease';
            successAlert.style.opacity = '0';
            setTimeout(() => Livewire.dispatch('clearMessage'), 600);
        }, 2000);
    }

    if (errorAlert) {
        setTimeout(() => {
            errorAlert.style.transition = 'opacity 0.6s ease';
            errorAlert.style.opacity = '0';
            setTimeout(() => Livewire.dispatch('clearMessage'), 600);
        }, 2000);
    }

    // Elements (now safe to query)
    const overlay = document.getElementById('profileOverlay');
    const sidebar = document.getElementById('profileSidebar');
    const closeBtn = document.getElementById('closeProfileSidebar');
    const userSearch = document.getElementById('userSearch');
    const linkedUsersBody = document.getElementById('linkedUsersBody');
    const profileRoleTitle = document.getElementById('profileRoleTitle');

    // helper: create safe text node (escape handled by textContent)
    function createCellWithAvatar(name, imageUrl) {
        const td = document.createElement('td');
        td.className = 'py-5 px-6 flex items-center gap-4';

        // Wrapper for avatar (ensures same size)
        const avatarWrapper = document.createElement('div');
        avatarWrapper.className = 'flex-shrink-0 w-10 h-10';

        if (imageUrl && imageUrl.trim() !== '') {
            // ✅ Show image
            const img = document.createElement('img');
            img.src = imageUrl;
            img.alt = name ?? 'User';
            img.className = 'w-10 h-10 rounded-full object-cover';
            avatarWrapper.appendChild(img);
        } else {
            // ✅ Show initials when image missing
            let initials = '?';
            if (name && name.trim() !== '') {
                const parts = name.trim().split(/\s+/);
                const first = parts[0] ? parts[0].charAt(0).toUpperCase() : '';
                const last = parts.length > 1 ? parts[parts.length - 1].charAt(0).toUpperCase() : '';
                initials = (first + last) || first || '?';
            }

            const initialsDiv = document.createElement('div');
            initialsDiv.className = 'w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-semibold';
            initialsDiv.textContent = initials;
            avatarWrapper.appendChild(initialsDiv);
        }

        // ✅ Add avatar wrapper first
        td.appendChild(avatarWrapper);

        // ✅ Then add user name text
        const span = document.createElement('span');
        span.className = 'text-gray-800 font-medium truncate max-w-[150px]';
        span.textContent = name ?? 'Unknown';
        td.appendChild(span);

        return td;
    }

    function createEmailCell(email) {
        const td = document.createElement('td');
        td.className = 'py-5 px-6 text-gray-500';
        td.textContent = email ?? '-';
        return td;
    }

    // Build a single tr element for a user object {name, email, image}
    function buildUserRow(u) {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50';

        const tdAvatar = createCellWithAvatar(u.name, u.image);
        const tdEmail = createEmailCell(u.email);

        tr.appendChild(tdAvatar);
        tr.appendChild(tdEmail);

        return tr;
    }

    // Clear & fill sidebar user list
    function fillUsersTable(users) {
        linkedUsersBody.innerHTML = ''; // clear

        if (!Array.isArray(users) || users.length === 0) {
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.colSpan = 2;
            td.className = 'py-6 text-gray-500 text-center';
            td.textContent = 'No users linked';
            tr.appendChild(td);
            linkedUsersBody.appendChild(tr);
            return;
        }

        users.forEach(u => {
            
            const row = buildUserRow(u);
            linkedUsersBody.appendChild(row);
        });
    }

    
    window.openProfileSidebar = function(roleName, users) {
        if (profileRoleTitle) profileRoleTitle.textContent = roleName ?? 'Role';
        fillUsersTable(users);
        // show overlay / sidebar
        if (sidebar) {
            sidebar.classList.remove('hidden');
            // ensure translate-x-full is removed so CSS transition runs
            setTimeout(() => sidebar.classList.remove('translate-x-full'), 10);
        }
        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.style.opacity = '0';
            setTimeout(() => overlay.style.opacity = '1', 10);
        }
    };

    // close handler
    function closeSidebar() {
        if (sidebar) sidebar.classList.add('translate-x-full');
        if (overlay) overlay.style.opacity = '0';
        setTimeout(() => {
            if (sidebar) sidebar.classList.add('hidden');
            if (overlay) overlay.classList.add('hidden');
        }, 400);
    }

    // events
    closeBtn?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    // client-side search filter
    userSearch?.addEventListener('input', () => {
        const filter = (userSearch.value || '').toLowerCase().trim();
        // iterate table rows
        Array.from(linkedUsersBody.querySelectorAll('tr')).forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
});
</script>

    @else
        <div class="p-10 text-center">
            <h2 class="text-2xl font-semibold text-red-600 mb-2">Access Denied</h2>
            <p class="text-gray-600">You are not authorized to view this page.</p>
        </div>
    @endrole
</div>
