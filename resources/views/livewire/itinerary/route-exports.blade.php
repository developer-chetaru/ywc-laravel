<div class="space-y-4">
    <h3 class="text-lg font-semibold text-gray-800">Export Route</h3>
    <p class="text-sm text-gray-600">Download this route in various formats for offline use or sharing.</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- PDF Export --}}
        <a
            href="{{ url('/api/itinerary/routes/' . $route->id . '/export/pdf') }}"
            class="flex flex-col items-center justify-center p-6 bg-red-50 border-2 border-red-200 rounded-lg hover:bg-red-100 transition-colors"
        >
            <div class="text-4xl mb-3">📄</div>
            <h4 class="font-semibold text-red-800 mb-1">PDF Document</h4>
            <p class="text-xs text-red-600 text-center">Printable route overview with all details</p>
        </a>

        {{-- GPX Export --}}
        <a
            href="{{ url('/api/itinerary/routes/' . $route->id . '/export/gpx') }}"
            class="flex flex-col items-center justify-center p-6 bg-blue-50 border-2 border-blue-200 rounded-lg hover:bg-blue-100 transition-colors"
        >
            <div class="text-4xl mb-3">🗺️</div>
            <h4 class="font-semibold text-blue-800 mb-1">GPX File</h4>
            <p class="text-xs text-blue-600 text-center">Navigation format for GPS devices and apps</p>
        </a>

        {{-- XLSX/CSV Export --}}
        <a
            href="{{ url('/api/itinerary/routes/' . $route->id . '/export/xlsx') }}"
            class="flex flex-col items-center justify-center p-6 bg-green-50 border-2 border-green-200 rounded-lg hover:bg-green-100 transition-colors"
        >
            <div class="text-4xl mb-3">📊</div>
            <h4 class="font-semibold text-green-800 mb-1">CSV Spreadsheet</h4>
            <p class="text-xs text-green-600 text-center">Data in Excel-compatible format</p>
        </a>
    </div>

    <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
        <p class="text-xs text-gray-600">
            <strong>Note:</strong> Exports include route information, stops, coordinates, and leg distances. 
            Weather data and crew information are not included in exports for privacy.
        </p>
    </div>
</div>

