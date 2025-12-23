@extends('layouts.app')

@section('content')
    <div class="py-8 bg-gray-50 min-h-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm flex items-start gap-2">
                    <span class="mt-0.5 font-semibold">Success:</span>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="px-6 py-5 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="space-y-1">
                        <div class="inline-flex items-center gap-2">
                            <h2 class="text-2xl font-semibold text-gray-900">Waitlist Entries</h2>
                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                                {{ $entries->total() }} total
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">
                            Manage early access requests, track status, and approve invites.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <form method="GET" action="{{ route('admin.waitlist') }}" class="flex items-center gap-2">
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</label>
                            <select
                                name="status"
                                class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white"
                                onchange="this.form.submit()"
                            >
                                @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'invited' => 'Invited'] as $value => $label)
                                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                        {{-- Simple client-side filter by email/name --}}
                        <div class="relative w-full sm:w-56">
                            <input
                                type="text"
                                id="waitlist-search"
                                placeholder="Search crew or email…"
                                class="w-full rounded-md border border-gray-300 pl-3 pr-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                oninput="filterWaitlistRows(this.value)"
                            >
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm align-middle" id="waitlist-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Crew</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Source</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Submitted</th>
                                <th class="px-6 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($entries as $entry)
                                <tr class="hover:bg-indigo-50/40 transition-colors" data-search-text="{{ strtolower(trim(($entry->first_name ?? '') . ' ' . ($entry->last_name ?? '') . ' ' . $entry->email)) }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $entry->first_name ?? '—' }} {{ $entry->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $entry->email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">
                                        @if($entry->role)
                                            <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-medium text-sky-700">
                                                {{ $entry->role }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs">Not specified</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @class([
                                                'bg-yellow-100 text-yellow-800' => $entry->status === 'pending',
                                                'bg-green-100 text-green-800' => $entry->status === 'approved',
                                                'bg-blue-100 text-blue-800' => $entry->status === 'invited',
                                            ])">
                                            {{ ucfirst($entry->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ str_replace('_', ' ', ucfirst($entry->source ?? 'Landing page')) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $entry->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                        <form
                                            method="POST"
                                            action="{{ route('admin.waitlist.update', $entry) }}"
                                            class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-end"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <select
                                                name="status"
                                                class="w-full lg:w-auto min-w-[110px] rounded-md border border-gray-300 bg-white px-2 py-1.5 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                @foreach(['pending', 'approved', 'invited'] as $option)
                                                    <option value="{{ $option }}" @selected($entry->status === $option)>{{ ucfirst($option) }}</option>
                                                @endforeach
                                            </select>
                                            <input
                                                type="text"
                                                name="notes"
                                                value="{{ old('notes', $entry->notes) }}"
                                                placeholder="Notes"
                                                class="w-full lg:w-56 rounded-md border border-gray-300 bg-white px-2 py-1.5 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                            <button
                                                type="submit"
                                                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none"
                                            >
                                                Save
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No waitlist entries found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                    <div>
                        Showing
                        <span class="font-medium text-gray-700">{{ $entries->firstItem() ?? 0 }}</span>
                        to
                        <span class="font-medium text-gray-700">{{ $entries->lastItem() ?? 0 }}</span>
                        of
                        <span class="font-medium text-gray-700">{{ $entries->total() }}</span>
                        entries
                    </div>
                    <div class="text-right">
                        {{ $entries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function filterWaitlistRows(query) {
        const q = (query || '').toLowerCase().trim();
        const rows = document.querySelectorAll('#waitlist-table tbody tr');

        rows.forEach(row => {
            const text = row.getAttribute('data-search-text') || '';
            const matches = !q || text.includes(q);
            row.style.display = matches ? '' : 'none';
        });
    }
</script>
@endpush

