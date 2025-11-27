@extends('layouts.app')

@section('content')
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Waitlist Entries</h2>
                        <p class="text-sm text-gray-500">Manage early access requests and approve invites.</p>
                    </div>
                    <form method="GET" action="{{ route('admin.waitlist') }}" class="flex items-center gap-3">
                        <label class="text-sm text-gray-600">Status:</label>
                        <select name="status" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" onchange="this.form.submit()">
                            @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'invited' => 'Invited'] as $value => $label)
                                <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Crew</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($entries as $entry)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $entry->first_name ?? '—' }} {{ $entry->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $entry->email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">
                                        {{ $entry->role ?? 'Not specified' }}
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
                                        <form method="POST" action="{{ route('admin.waitlist.update', $entry) }}" class="flex flex-col gap-2 md:flex-row md:items-center">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                                @foreach(['pending', 'approved', 'invited'] as $option)
                                                    <option value="{{ $option }}" @selected($entry->status === $option)>{{ ucfirst($option) }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="notes" value="{{ old('notes', $entry->notes) }}"
                                                placeholder="Notes"
                                                class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm w-full md:w-48">
                                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none">
                                                Update
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

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $entries->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

