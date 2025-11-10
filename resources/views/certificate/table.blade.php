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
                <td class="border p-2 flex justify-center gap-3 items-center">
                    <a href="{{ route('certificate-type.edit', $certificate->id) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" onclick="openDeleteModal({{ $certificate->id }})" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="border p-2 text-center">No Certificate Types Found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-6">
    {{ $types->links() }}
</div>
