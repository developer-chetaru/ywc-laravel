<?php

namespace App\Services\Itinerary;

use App\Models\ItineraryRoute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RouteExporter
{
    public function exportPDF(ItineraryRoute $route): string
    {
        // Ensure stops are loaded with photos
        $route->load('stops');
        
        $html = view('itinerary.exports.pdf', ['route' => $route])->render();
        
        // Simple HTML to PDF using browser print or return HTML for download
        // For production, you might want to use dompdf or similar
        $filename = 'route_' . Str::slug($route->title) . '_' . now()->format('Y-m-d') . '.html';
        $path = 'exports/' . $filename;
        
        // Ensure exports directory exists
        if (!Storage::disk('public')->exists('exports')) {
            Storage::disk('public')->makeDirectory('exports');
        }
        
        Storage::disk('public')->put($path, $html);
        
        return Storage::disk('public')->path($path);
    }

    public function exportGPX(ItineraryRoute $route): string
    {
        // Ensure stops are loaded
        $route->load('stops');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<gpx version="1.1" creator="YachtCrew Itinerary System" xmlns="http://www.topografix.com/GPX/1/1">' . "\n";
        $xml .= '  <metadata>' . "\n";
        $xml .= '    <name>' . htmlspecialchars($route->title) . '</name>' . "\n";
        $xml .= '    <desc>' . htmlspecialchars($route->description ?? '') . '</desc>' . "\n";
        $xml .= '  </metadata>' . "\n";
        $xml .= '  <rte>' . "\n";
        $xml .= '    <name>' . htmlspecialchars($route->title) . '</name>' . "\n";
        
        foreach ($route->stops as $stop) {
            if ($stop->latitude && $stop->longitude) {
                $xml .= '    <rtept lat="' . $stop->latitude . '" lon="' . $stop->longitude . '">' . "\n";
                $xml .= '      <name>' . htmlspecialchars($stop->name) . '</name>' . "\n";
                if ($stop->location_label) {
                    $xml .= '      <desc>' . htmlspecialchars($stop->location_label) . '</desc>' . "\n";
                }
                if ($stop->notes) {
                    $xml .= '      <cmt>' . htmlspecialchars($stop->notes) . '</cmt>' . "\n";
                }
                $xml .= '    </rtept>' . "\n";
            }
        }
        
        $xml .= '  </rte>' . "\n";
        $xml .= '</gpx>';
        
        $filename = 'route_' . Str::slug($route->title) . '_' . now()->format('Y-m-d') . '.gpx';
        $path = 'exports/' . $filename;
        
        // Ensure exports directory exists
        if (!Storage::disk('public')->exists('exports')) {
            Storage::disk('public')->makeDirectory('exports');
        }
        
        Storage::disk('public')->put($path, $xml);
        
        return Storage::disk('public')->path($path);
    }

    public function exportXLSX(ItineraryRoute $route): string
    {
        // Ensure stops and legs are loaded
        $route->load(['stops', 'legs.from', 'legs.to']);
        
        $csv = [];
        
        // Header
        $csv[] = ['Route Information'];
        $csv[] = ['Title', $route->title];
        $csv[] = ['Description', $route->description ?? ''];
        $csv[] = ['Region', $route->region ?? ''];
        $csv[] = ['Difficulty', $route->difficulty ?? ''];
        $csv[] = ['Season', $route->season ?? ''];
        $csv[] = ['Duration (Days)', $route->duration_days];
        $csv[] = ['Distance (NM)', number_format($route->distance_nm, 2)];
        $csv[] = ['Status', $route->status];
        $csv[] = ['Visibility', $route->visibility];
        $csv[] = [];
        
        // Stops
        $csv[] = ['Stops'];
        $csv[] = ['#', 'Name', 'Location', 'Latitude', 'Longitude', 'Stay Duration (hrs)', 'Notes', 'Photos Count'];
        
        foreach ($route->stops as $stop) {
            $photosCount = is_array($stop->photos) ? count($stop->photos) : 0;
            $csv[] = [
                $stop->sequence,
                $stop->name,
                $stop->location_label ?? '',
                $stop->latitude ?? '',
                $stop->longitude ?? '',
                $stop->stay_duration_hours ?? '',
                $stop->notes ?? '',
                $photosCount,
            ];
        }
        
        $csv[] = [];
        
        // Legs
        if ($route->legs->isNotEmpty()) {
            $csv[] = ['Legs'];
            $csv[] = ['From', 'To', 'Distance (NM)', 'Estimated Hours'];
            
            foreach ($route->legs as $leg) {
                $csv[] = [
                    optional($leg->from)->name ?? '',
                    optional($leg->to)->name ?? '',
                    number_format($leg->distance_nm, 2),
                    $leg->estimated_hours ?? '',
                ];
            }
        }
        
        // Convert to CSV string
        $output = fopen('php://temp', 'r+');
        foreach ($csv as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        $filename = 'route_' . Str::slug($route->title) . '_' . now()->format('Y-m-d') . '.csv';
        $path = 'exports/' . $filename;
        
        // Ensure exports directory exists
        if (!Storage::disk('public')->exists('exports')) {
            Storage::disk('public')->makeDirectory('exports');
        }
        
        Storage::disk('public')->put($path, $csvContent);
        
        return Storage::disk('public')->path($path);
    }
}

