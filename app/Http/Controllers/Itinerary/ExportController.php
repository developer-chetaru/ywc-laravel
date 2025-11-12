<?php

namespace App\Http\Controllers\Itinerary;

use App\Http\Controllers\Controller;
use App\Models\ItineraryRoute;
use App\Services\Itinerary\RouteExporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    public function __construct(
        protected RouteExporter $exporter
    ) {
        // Middleware is applied in routes/web.php and routes/api.php
    }

    public function pdf(Request $request, ItineraryRoute $route): BinaryFileResponse|JsonResponse
    {
        Gate::authorize('view', $route);

        $filePath = $this->exporter->exportPDF($route);
        $filename = 'route_' . \Illuminate\Support\Str::slug($route->title) . '_' . now()->format('Y-m-d') . '.html';

        // For API requests (mobile app), return JSON with download URL
        if ($request->wantsJson() || $request->is('api/*')) {
            $url = Storage::disk('public')->url(str_replace(storage_path('app/public/'), '', $filePath));
            
            return response()->json([
                'success' => true,
                'message' => 'PDF export generated successfully',
                'data' => [
                    'download_url' => $url,
                    'filename' => $filename,
                    'file_path' => str_replace(storage_path('app/public/'), '', $filePath),
                ],
            ]);
        }

        // For web requests, return file download
        return response()->download($filePath, $filename, [
            'Content-Type' => 'text/html',
        ])->deleteFileAfterSend(true);
    }

    public function gpx(Request $request, ItineraryRoute $route): BinaryFileResponse|JsonResponse
    {
        Gate::authorize('view', $route);

        $filePath = $this->exporter->exportGPX($route);
        $filename = 'route_' . \Illuminate\Support\Str::slug($route->title) . '_' . now()->format('Y-m-d') . '.gpx';

        // For API requests (mobile app), return JSON with download URL
        if ($request->wantsJson() || $request->is('api/*')) {
            $url = Storage::disk('public')->url(str_replace(storage_path('app/public/'), '', $filePath));
            
            return response()->json([
                'success' => true,
                'message' => 'GPX export generated successfully',
                'data' => [
                    'download_url' => $url,
                    'filename' => $filename,
                    'file_path' => str_replace(storage_path('app/public/'), '', $filePath),
                ],
            ]);
        }

        // For web requests, return file download
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/gpx+xml',
        ])->deleteFileAfterSend(true);
    }

    public function xlsx(Request $request, ItineraryRoute $route): BinaryFileResponse|JsonResponse
    {
        Gate::authorize('view', $route);

        $filePath = $this->exporter->exportXLSX($route);
        $filename = 'route_' . \Illuminate\Support\Str::slug($route->title) . '_' . now()->format('Y-m-d') . '.csv';

        // For API requests (mobile app), return JSON with download URL
        if ($request->wantsJson() || $request->is('api/*')) {
            $url = Storage::disk('public')->url(str_replace(storage_path('app/public/'), '', $filePath));
            
            return response()->json([
                'success' => true,
                'message' => 'XLSX export generated successfully',
                'data' => [
                    'download_url' => $url,
                    'filename' => $filename,
                    'file_path' => str_replace(storage_path('app/public/'), '', $filePath),
                ],
            ]);
        }

        // For web requests, return file download
        return response()->download($filePath, $filename, [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    }
}

