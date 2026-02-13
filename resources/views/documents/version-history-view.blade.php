<div class="space-y-4">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-end mb-4">
            <span class="text-sm text-gray-600">
                Current Version: <strong>v{{ $document->version }}</strong>
            </span>
        </div>

        @if($versions->count() > 0)
        <div class="space-y-3">
            @foreach($versions as $version)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                Version {{ $version->version_number }}
                            </span>
                            @if($version->version_number == $document->version)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
                                Current
                            </span>
                            @endif
                            <span class="text-sm text-gray-500">
                                {{ $version->created_at->format('M d, Y h:i A') }}
                            </span>
                        </div>

                        @if($version->creator)
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-user mr-1"></i>
                            Created by: {{ $version->creator->name ?? $version->creator->email }}
                        </p>
                        @endif

                        @if($version->change_notes)
                        <p class="text-sm text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-1"></i>
                            {{ $version->change_notes }}
                        </p>
                        @endif

                        <div class="flex items-center gap-4 text-xs text-gray-500 mt-2">
                            @if($version->file_size)
                            <span>
                                <i class="fas fa-file mr-1"></i>
                                {{ $version->formatted_file_size }}
                            </span>
                            @endif
                            @if($version->file_type)
                            <span>
                                <i class="fas fa-file-alt mr-1"></i>
                                {{ strtoupper($version->file_type) }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="ml-4 flex gap-2">
                        @if($version->file_path)
                        <a href="{{ route('documents.versions.download', $version->id) }}"
                           class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm font-medium"
                           title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        @endif
                        @if($version->version_number != $document->version)
                        <form action="{{ route('documents.restore-version', ['document' => $document->id, 'version' => $version->id]) }}" 
                              method="POST" 
                              onsubmit="return confirm('Restore this version? Current version will be saved first.');">
                            @csrf
                            <button type="submit"
                                    class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm font-medium"
                                    title="Restore">
                                <i class="fas fa-undo"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-history text-4xl mb-3"></i>
            <p>No version history available yet.</p>
        </div>
        @endif
    </div>
</div>
