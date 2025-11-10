<div class="w-full h-screen">
    @if ($iframeUrl)
        <iframe 
            src="{{ $iframeUrl }}" 
            class="w-full h-full border-0"
            style="min-height: 100vh;" 
            frameborder="0">
        </iframe>
    @else
        <p class="text-red-500 mt-4">publicProfileIframeUrl not found</p>
    @endif
</div>
