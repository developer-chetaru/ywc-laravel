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

        $result = $checker->checkCertificate($request->doc_number, $request->dob);

        if ($result['status'] === 'found') {
            $document->update(['status' => 'approved']);
        } else {
            $document->update(['status' => 'rejected']);
        }

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
