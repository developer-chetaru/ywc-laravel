@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 text-sm font-medium text-gray-400 bg-gray-100 rounded-md cursor-default">
                Prev
            </span>
        @else
            <button wire:click="previousPage" wire:loading.attr="disabled"
                class="px-3 py-1 text-sm font-medium text-white bg-[#0053FF] rounded-md hover:bg-[#003BB5]">
                Prev
            </button>
        @endif

        {{-- Pagination Elements --}}
        <div class="flex items-center space-x-1 mx-2">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-1 text-sm text-gray-500">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1 text-sm font-medium text-white bg-[#0053FF] rounded-md">{{ $page }}</span>
                        @else
                            <button wire:click="gotoPage({{ $page }})" class="px-3 py-1 text-sm font-medium text-[#0053FF] bg-white border border-[#0053FF] rounded-md hover:bg-[#0053FF] hover:text-white">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" wire:loading.attr="disabled"
                class="px-3 py-1 text-sm font-medium text-white bg-[#0053FF] rounded-md hover:bg-[#003BB5]">
                Next
            </button>
        @else
            <span class="px-3 py-1 text-sm font-medium text-gray-400 bg-gray-100 rounded-md cursor-default">
                Next
            </span>
        @endif
    </nav>
@endif
