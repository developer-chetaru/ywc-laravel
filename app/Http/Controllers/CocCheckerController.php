<?php
namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\CocCheckerService;
use Illuminate\Http\Request;

class CocCheckerController extends Controller
{
    public function verify(Request $request, CocCheckerService $checker, $documentId)
    {
        $request->validate([
            'dob' => 'required|date',
            'doc_number' => 'required|string'
        ]);

        $document = Document::findOrFail($documentId);

        // Only allow verification for certificate type documents
        // The CocCheckerService only works with UK Certificate of Competency (CoC) database
        if ($document->type !== 'certificate') {
            return response()->json([
                'status' => 'error',
                'message' => 'Verification is only available for Certificate documents. This service checks UK Certificate of Competency (CoC) database. Please use "Change Status" to manually approve/reject other document types.'
            ], 400);
        }

        $result = $checker->checkCertificate($request->doc_number, $request->dob);

        // Only auto-approve if found, but don't auto-reject if not found
        // Let admin manually decide to reject after verification fails
        if ($result['status'] === 'found') {
            $document->update([
                'status' => 'approved',
                'updated_by' => auth()->id()
            ]);
        }
        // If not found or error, don't change status - let admin decide manually

        return response()->json($result);
    }


    public function updateStatus(Request $request, Document $document)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $document->status = $request->status;
        $document->updated_by = auth()->id();
        $document->save();

        return response()->json(['success' => true]);
    }
}
