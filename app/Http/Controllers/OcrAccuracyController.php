<?php

namespace App\Http\Controllers;

use App\Models\OcrAccuracyLog;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OcrAccuracyController extends Controller
{
    /**
     * Display OCR accuracy dashboard
     */
    public function index(Request $request)
    {
        $filters = [
            'document_type' => $request->get('document_type'),
            'ocr_engine' => $request->get('ocr_engine'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $stats = OcrAccuracyLog::getAccuracyStats(array_filter($filters));

        // Recent corrections
        $recentLogs = OcrAccuracyLog::with(['document', 'user'])
            ->latest()
            ->limit(20)
            ->get();

        // Accuracy trend over time
        $trend = OcrAccuracyLog::selectRaw('DATE(created_at) as date, AVG(overall_accuracy) as accuracy')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        // Low accuracy documents
        $lowAccuracyDocs = OcrAccuracyLog::with('document')
            ->where('overall_accuracy', '<', 70)
            ->latest()
            ->limit(10)
            ->get();

        return view('ocr.accuracy-dashboard', compact('stats', 'recentLogs', 'trend', 'lowAccuracyDocs', 'filters'));
    }

    /**
     * View detailed log
     */
    public function show($id)
    {
        $log = OcrAccuracyLog::with(['document', 'user'])->findOrFail($id);

        return view('ocr.accuracy-details', compact('log'));
    }

    /**
     * Export accuracy report
     */
    public function export(Request $request)
    {
        $filters = [
            'document_type' => $request->get('document_type'),
            'ocr_engine' => $request->get('ocr_engine'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $query = OcrAccuracyLog::with(['document', 'user']);

        foreach (array_filter($filters) as $key => $value) {
            if ($key === 'date_from') {
                $query->where('created_at', '>=', $value);
            } elseif ($key === 'date_to') {
                $query->where('created_at', '<=', $value);
            } else {
                $query->where($key, $value);
            }
        }

        $logs = $query->get();

        $csv = "Document,Type,OCR Engine,Overall Accuracy,Fields Correct,Fields Total,Corrected At\n";

        foreach ($logs as $log) {
            $csv .= sprintf(
                "%s,%s,%s,%.2f,%d,%d,%s\n",
                $log->document->document_name ?? 'N/A',
                $log->document_type ?? 'N/A',
                $log->ocr_engine ?? 'N/A',
                $log->overall_accuracy,
                $log->fields_correct,
                $log->fields_total,
                $log->corrected_at ? $log->corrected_at->format('Y-m-d H:i:s') : 'N/A'
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ocr_accuracy_report_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Get accuracy stats API
     */
    public function stats(Request $request)
    {
        $filters = [
            'document_type' => $request->get('document_type'),
            'ocr_engine' => $request->get('ocr_engine'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $stats = OcrAccuracyLog::getAccuracyStats(array_filter($filters));

        return response()->json($stats);
    }
}
