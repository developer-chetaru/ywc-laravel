<?php

namespace App\Http\Controllers\CareerHistory;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CareerHistoryEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SeaServiceReportController extends Controller
{
    /**
     * Generate and download Sea Service Report as PDF
     */
    public function download(Request $request, $userId = null)
    {
        $currentUser = Auth::user();
        
        // Determine which user's report to generate
        if ($userId && $currentUser->hasRole('super_admin')) {
            $user = User::findOrFail($userId);
        } else {
            $user = $currentUser;
        }

        // Get all career history entries
        $entries = CareerHistoryEntry::where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get();

        // Calculate sea service totals
        $totalDays = 0;
        $totalYears = 0;
        $totalMonths = 0;
        $entriesWithService = [];

        foreach ($entries as $entry) {
            if ($entry->qualifiesForSeaService()) {
                $days = $entry->getSeaServiceDays();
                $totalDays += $days;
                
                $months = $entry->getDurationInMonths();
                $years = floor($months / 12);
                $remainingMonths = $months % 12;
                
                $entriesWithService[] = [
                    'entry' => $entry,
                    'days' => $days,
                    'years' => $years,
                    'months' => $remainingMonths,
                ];
            }
        }

        // Calculate totals
        $totalYears = floor($totalDays / 365);
        $remainingDays = $totalDays % 365;
        $totalMonths = floor($remainingDays / 30);
        $finalDays = $remainingDays % 30;

        // Prepare data for PDF
        $data = [
            'user' => $user,
            'entries' => $entriesWithService,
            'totalDays' => $totalDays,
            'totalYears' => $totalYears,
            'totalMonths' => $totalMonths,
            'finalDays' => $finalDays,
            'generatedAt' => Carbon::now()->format('F d, Y'),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('career-history.sea-service-report', $data);
        
        $filename = 'sea-service-report-' . $user->id . '-' . Carbon::now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * View Sea Service Report in browser
     */
    public function view(Request $request, $userId = null)
    {
        $currentUser = Auth::user();
        
        // Determine which user's report to generate
        if ($userId && $currentUser->hasRole('super_admin')) {
            $user = User::findOrFail($userId);
        } else {
            $user = $currentUser;
        }

        // Get all career history entries
        $entries = CareerHistoryEntry::where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get();

        // Calculate sea service totals
        $totalDays = 0;
        $entriesWithService = [];

        foreach ($entries as $entry) {
            if ($entry->qualifiesForSeaService()) {
                $days = $entry->getSeaServiceDays();
                $totalDays += $days;
                
                $months = $entry->getDurationInMonths();
                $years = floor($months / 12);
                $remainingMonths = $months % 12;
                
                $entriesWithService[] = [
                    'entry' => $entry,
                    'days' => $days,
                    'years' => $years,
                    'months' => $remainingMonths,
                ];
            }
        }

        // Calculate totals
        $totalYears = floor($totalDays / 365);
        $remainingDays = $totalDays % 365;
        $totalMonths = floor($remainingDays / 30);
        $finalDays = $remainingDays % 30;

        return view('career-history.sea-service-report', [
            'user' => $user,
            'entries' => $entriesWithService,
            'totalDays' => $totalDays,
            'totalYears' => $totalYears,
            'totalMonths' => $totalMonths,
            'finalDays' => $finalDays,
            'generatedAt' => Carbon::now()->format('F d, Y'),
        ]);
    }
}
