<div class="[&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] h-[calc(100vh-195px)] overflow-y-auto">
    <!-- 🔹 Back Button -->
    <div class="flex gap-4 mb-6">
        <button type="button" class="backToChat cursor-pointer bg-white border border-[#808080] flex gap-2 justify-center items-center px-4 py-2 rounded-md !text-[#808080] hover:!text-blue-600 hover:!border-blue-600 transition text-sm">
            <img class="h-3" src="/images/left-arr.svg" alt="">
            Back to Department Forums
        </button>
    </div>

    {{-- ✅ Success message (auto hides after 2 sec, no Alpine) --}}
    @if (session()->has('success'))
        <div id="successMessage"
            class="mb-4 p-3 text-green-800 bg-green-100 border border-green-300 rounded-lg transition-opacity duration-500">
            {{ session('success') }}
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const success = document.getElementById('successMessage');
                if (success) {
                    setTimeout(() => {
                        success.style.opacity = '0';
                        setTimeout(() => success.remove(), 500);
                    }, 2000);
                }
            });
        </script>
    @endif


    <div class=" pr-2 ">
    <form wire:submit.prevent="store" 
        class="w-full bg-white rounded-lg border border-[#808080] p-8">

        <div class="mb-5">
            <label class="block text-[#1B1B1B] text-sm font-normal mb-2 capitalize">Forum Name</label>
            <input type="text" wire:model.defer="title" placeholder="e.g. Onboard Safety Forum"
                class="w-full border border-[#BDBDBD] rounded-md px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none placeholder-[#808080]" />
            @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-5">
            <label class="block text-[#1B1B1B] text-sm font-normal mb-1 capitalize">
                Forum Description <span class="italic">(admin-only)</span>
            </label>
            <textarea wire:model.defer="description" rows="3" placeholder="Briefly describe the purpose of this forum…"
                class="resize-none h-[80px] w-full border border-[#BDBDBD] rounded-md px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none placeholder-[#808080]"></textarea>
        </div>

        <div class="mb-8">
            <label class="block text-[#1B1B1B] text-sm font-normal mb-2 capitalize">Parent Category</label>
            <select wire:model.defer="parent_category"
                class="w-full border border-[#BDBDBD] rounded-md px-4 py-3 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm text-[#808080]">
                <option value="">Top-Level Forum</option>
                @foreach($availableCategories as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            @error('parent_category') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="bg-[#F5F6FA] rounded-lg p-5 mb-8">
            <h3 class="text-[#1B1B1B] text-sm font-medium mb-4">Initial Thread Setup</h3>

            <div class="mb-5">
                <label class="block text-gray-800 text-sm font-normal mb-2 capitalize">Thread Title</label>
                <input type="text" wire:model.defer="threadTitle" placeholder="e.g. How often should we conduct fire drills?"
                    class="bg-white w-full border border-[#BDBDBD] rounded-md px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none placeholder-[#808080]" />
            </div>

            <div>
                <label class="block text-[#1B1B1B] text-sm font-normal mb-2 capitalize">Thread chat</label>
                <input type="text" wire:model.defer="threadDescription" placeholder="Enter a short summary or discussion starter…"
                    class="bg-white w-full border border-[#BDBDBD] rounded-md px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none placeholder-[#808080]" />
            </div>
        </div>

        {{-- ✅ Add Users Button --}}
        <div class="mb-5">
            <button type="button" wire:click="toggleUsersList"
                class="cursor-pointer flex items-center gap-2 text-[#1B1B1B] font-medium text-sm mb-3 hover:text-[#0053FF] transition">
                <img src="/images/user.svg" alt="User" class="w-5 h-5">
                Add access to
            </button>
        </div>

        {{-- ✅ Role List Section --}}
        @if ($showUsersList)
            <div class="mb-8">
                <div class="bg-[#F5F6FA] rounded-lg p-4 px-5">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Add Roles (for Forum Access)
                    </label>

                    <!-- Search -->
                    <div class="relative mb-4">
                        <input type="search" wire:model.live="searchRole" placeholder="Search roles by name"
                            class="text-[#808080] placeholder-[#808080] w-full py-3 px-4 rounded-lg border border-[#E5E5E5] focus:outline-none focus:border-blue-200 text-sm !pl-[40px] font-medium bg-[#F8F9FA]">
                        <button type="button" class="absolute left-3 top-[14px]">
                            <img src="/images/search.svg" alt="Search" class="w-4 h-4">
                        </button>
                    </div>

                    <!-- Role List -->
                    <div class="max-h-60 overflow-y-auto rounded-lg p-5 pr-2 bg-white border border-[#BDBDBD] custom-scrollbar">
                        <ul class="space-y-3 text-sm text-[#808080] pl-[16px]">
                            @forelse ($roles as $role)
                                <li class="flex items-center gap-2">
                                    <input type="checkbox" wire:model="selectedRoles" value="{{ $role->id }}"
                                        class="w-[14px] h-[14px] text-blue-600 border-[#616161] rounded">
                                    <span>{{ $role->name }}</span>
                                </li>
                            @empty
                                <li class="text-gray-500 italic">No roles found.</li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Selected Tags -->
                    @if (!empty($selectedRoles))
                        <div class="flex flex-wrap gap-[12px] mt-4">
                            @foreach ($selectedRoles as $id)
                                @php $r = $roles->firstWhere('id', $id); @endphp
                                @if ($r)
                                    <span
                                        class="flex items-center bg-white text-[#0053FF] px-3 py-2 text-sm border border-[#BDBDBD] rounded">
                                        {{ $r->name }}
                                        <button wire:click="removeRole({{ $id }})" type="button"
                                            class="ml-3 text-[#808080] hover:text-red-500">✕</button>
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif


        {{-- ✅ Footer Buttons --}}
        <div class="flex flex-wrap items-center gap-3  bg-white  pb-2">
            <button type="submit" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white text-sm font-normal px-6 py-3 rounded-lg shadow-sm">
                Save & Create Forum
            </button>
            <button type="button" wire:click="resetForm" class="cursor-pointer text-[#616161] text-sm font-normal hover:text-gray-700">
                Reset form
            </button>
        </div>
    </form>
</div>

<style>
    /* ✅ Custom Scrollbar Style (optional) */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #BDBDBD;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #F1F1F1;
    }
</style>

</div>