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
        <form method="GET" action="{{ route('certificate-types.index') }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search certificate type..." class="border p-2 rounded w-full"/>
        </form>
    </div>

    <!-- Table -->
    <table class="w-full border-collapse border border-gray-200">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">#</th>
                <th class="border p-2 text-left">Name</th>
                <th class="border p-2 text-center">Active</th>
                <th class="border p-2 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($types as $index => $certificate)
                <tr>
                    <td class="border p-2">{{ $types->firstItem() + $index }}</td>
                    <td class="border p-2">{{ $certificate->name }}</td>
                    <td class="border p-2 text-center">
                        <form method="POST" action="{{ route('certificate-type.toggle', ['id' => $certificate->id]) }}">
                            @csrf
                            @method('PATCH')
                            <input type="checkbox"
                                onchange="this.form.submit()"
                                {{ $certificate->is_active ? 'checked' : '' }}
                                class="h-4 w-4 text-[#0053FF] rounded cursor-pointer">
                        </form>
                    </td>

                    <td class="border p-2 flex justify-center gap-2 items-center">
                        <a href="{{ route('certificate-type.edit', $certificate->id) }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form action="{{ route('certificate-type.destroy', $certificate->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="border p-2 text-center">No Certificate Types Found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $types->appends(request()->input())->links() }}
    </div>

</div>
@else
<div class="p-6 bg-white rounded-lg shadow-md">
    <p class="text-gray-600 text-lg font-medium">You do not have permission to view this page.</p>
</div>
@endrole
@endsection

