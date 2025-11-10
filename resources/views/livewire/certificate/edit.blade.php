@extends('layouts.app-laravel')

@section('content')
<div class="p-6 bg-white rounded-lg shadow-md w-full mx-auto mt-1">
    <h2 class="text-xl font-bold text-[#0053FF] mb-6">Edit Certificate Type with Issuers</h2>

    @if(session()->has('message'))
        <div class="text-[#0053FF] mb-4">{{ session('message') }}</div>
    @endif

    <form action="{{ route('certificate-type.update', $certificateType->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Certificate Type Name -->
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Certificate Type Name</label>
            <input type="text" name="name" value="{{ old('name', $certificateType->name) }}" placeholder="Enter certificate type"
                   class="border p-2 rounded w-full"/>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Issuers Dynamic Table -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <label class="text-gray-700 font-semibold">Certificate Issuers</label>
                <button type="button" onclick="addRow()" class="px-2 py-2 bg-[#0053FF] text-white rounded hover:bg-blue-700 text-sm">
                    Add Issuer
                </button>
            </div>

            <div class="overflow-y-auto max-h-[60vh] border border-gray-300 rounded-md">
                <table class="w-full border-collapse" id="issuersTable">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border p-2 text-left">Issuer Name</th>
                            <th class="border p-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($certificateType->issuers as $index => $issuer)
                            <tr>
                                <td class="border p-2">
                                    <select name="issuers[{{ $index }}][name]" class="border p-2 rounded w-full searchable-select">
                                        <option value="">Select issuer</option>
                                        @foreach($allIssuers as $allIssuer)
                                            <option value="{{ $allIssuer->name }}"
                                                {{ $allIssuer->id == $issuer->id ? 'selected' : '' }}>
                                                {{ $allIssuer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="border p-2 text-center">
                                    <button type="button" onclick="removeRow(this)" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-[#0053FF] text-white rounded hover:bg-blue-700">Update</button>
            <a href="{{ route('certificate-types.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</a>
        </div>
    </form>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    let rowIndex = {{ $certificateType->issuers->count() }};

    function addRow() {
        const tableBody = document.getElementById('issuersTable').getElementsByTagName('tbody')[0];
        const newRow = document.createElement('tr');

        let options = `@foreach($allIssuers as $issuer)<option value="{{ $issuer->name }}">{{ $issuer->name }}</option>@endforeach`;

        newRow.innerHTML = `
            <td class="border p-2">
                <select name="issuers[${rowIndex}][name]" class="border p-2 rounded w-full searchable-select">
                    <option value="">Select issuer</option>
                    ${options}
                </select>
            </td>
            <td class="border p-2 text-center">
                <button type="button" onclick="removeRow(this)" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
            </td>
        `;

        tableBody.appendChild(newRow);
        initializeChoices();
        rowIndex++;
    }

    function removeRow(button) {
        const row = button.closest('tr');
        row.remove();
    }

    function initializeChoices() {
        document.querySelectorAll('.searchable-select').forEach(function(el) {
            if (!el.classList.contains('choices-initialized')) {
                new Choices(el, { searchEnabled: true, itemSelectText: '' });
                el.classList.add('choices-initialized');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initializeChoices();
    });
</script>
@endsection