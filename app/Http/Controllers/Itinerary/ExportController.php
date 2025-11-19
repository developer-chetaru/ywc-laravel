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

    /**
     * @OA\Get(
     *     path="/api/itinerary/routes/{route}/export/pdf",
     *     summary="Export route as PDF",
     *     tags={"Itinerary Export"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PDF export generated (returns JSON with download URL for API requests)",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="PDF export generated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="download_url", type="string", example="/storage/exports/route_123.pdf"),
     *                 @OA\Property(property="filename", type="string", example="route_mediterranean_2024-11-19.html"),
     *                 @OA\Property(property="file_path", type="string", example="exports/route_123.pdf")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/itinerary/routes/{route}/export/gpx",
     *     summary="Export route as GPX",
     *     tags={"Itinerary Export"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="GPX export generated (returns JSON with download URL for API requests)",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="GPX export generated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="download_url", type="string", example="/storage/exports/route_123.gpx"),
     *                 @OA\Property(property="filename", type="string", example="route_mediterranean_2024-11-19.gpx"),
     *                 @OA\Property(property="file_path", type="string", example="exports/route_123.gpx")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/itinerary/routes/{route}/export/xlsx",
     *     summary="Export route as XLSX/CSV",
     *     tags={"Itinerary Export"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Route ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="XLSX export generated (returns JSON with download URL for API requests)",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="XLSX export generated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="download_url", type="string", example="/storage/exports/route_123.csv"),
     *                 @OA\Property(property="filename", type="string", example="route_mediterranean_2024-11-19.csv"),
     *                 @OA\Property(property="file_path", type="string", example="exports/route_123.csv")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied")
     * )
     */
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

