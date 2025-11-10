@extends('layouts.app-laravel')

@section('content')
@role('super_admin')
<div class="p-6 bg-white rounded-lg shadow-md">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-[#0053FF]">Certificate Types</h2>
        <a href="{{ route('certificate-type.create') }}" class="px-4 py-2 bg-[#0053FF] text-white rounded hover:bg-blue-700">Add</a>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <form id="searchForm" method="GET" action="{{ route('certificate-types.index') }}">
            <input
                type="text"
                id="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search certificate type..."
                class="border p-2 rounded w-full"
            />
        </form>
    </div>

    <!-- Table container -->
    <div id="table-container">
        @include('certificate.table', ['types' => $types])
    </div>

</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-xl shadow-xl w-[95%] max-w-md p-6 text-center">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Confirm Delete</h3>
        <p class="text-gray-600 mb-6">Are you sure you want to delete this record? This action cannot be undone.</p>

        <div class="flex justify-center gap-4">
            <button onclick="closeDeleteModal()" 
                class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
                Cancel
            </button>

            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-5 py-2 rounded-lg bg-[#0053FF] text-white hover:bg-[#0053FF]0">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openDeleteModal(id) {
    let modal = document.getElementById('deleteModal');
    let form = document.getElementById('deleteForm');
    form.action = '/certificate-type/' + id;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDeleteModal() {
    let modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('search');
    let debounceTimeout = null;

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(function () {
            const query = searchInput.value;

            fetch(`{{ route('certificate-types.index') }}?search=${query}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('table-container').innerHTML = html;
            });
        }, 500);
    });
});
</script>
@else
<div class="p-6 bg-white rounded-lg shadow-md">
    <p class="text-gray-600 text-lg font-medium">You do not have permission to view this page.</p>
</div>
@endrole
@endsection
