<?php

namespace App\Http\Controllers;

use App\Models\CertificateType;
use App\Models\CertificateIssuer;
use Illuminate\Http\Request;

class CertificateTypeController extends Controller
{
    public function create()
    {
        $allIssuers = CertificateIssuer::where('is_active', 1)->orderBy('name')->get();
        return view('livewire.certificate.create', compact('allIssuers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:certificate_types,name',
            'issuers.*.name' => 'required|string|max:255',
        ]);

        // Create the certificate type
        $certificateType = CertificateType::create([
            'name' => $request->name,
            'is_active' => true,
        ]);

        // Process each issuer
        if ($request->has('issuers')) {
            foreach ($request->issuers as $issuerData) {
                // Find or create the issuer by name
                $issuer = CertificateIssuer::firstOrCreate(
                    ['name' => $issuerData['name']],
                    ['is_active' => true]
                );

                // Attach the issuer to the certificate type
                $certificateType->issuers()->syncWithoutDetaching($issuer->id);
            }
        }

        return redirect()->route('certificate-types.index')->with('message', 'Certificate Type and issuers added successfully!');
    }
  
  	public function toggleActive($id)
    {
        $type = CertificateType::findOrFail($id);

        // Toggle the is_active value
        $type->is_active = !$type->is_active;
        $type->save();

        // Optionally return back with a success message
        return back()->with('message', 'Certificate type status updated successfully.');
    }

    public function edit($id)
    {
        $certificateType = CertificateType::with('issuers')->findOrFail($id);
        $allIssuers = CertificateIssuer::where('is_active', 1)->orderBy('name')->get();

        return view('livewire.certificate.edit', compact('certificateType', 'allIssuers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:certificate_types,name,' . $id,
            'issuers.*.name' => 'required|string|max:255',
        ]);

        $certificateType = CertificateType::findOrFail($id);
        $certificateType->name = $request->name;
        $certificateType->save();

        // Process each issuer
        $issuerIds = [];
        if ($request->has('issuers')) {
            foreach ($request->issuers as $issuerData) {
                $issuer = CertificateIssuer::firstOrCreate(
                    ['name' => $issuerData['name']],
                    ['is_active' => true]
                );
                $issuerIds[] = $issuer->id;
            }
        }

        // Sync issuers (attach new and detach removed ones)
        $certificateType->issuers()->sync($issuerIds);

        return redirect()->route('certificate-types.index')->with('message', 'Certificate Type updated successfully!');
    }
  
  	public function destroy($id)
    {
        $certificate = CertificateType::findOrFail($id);
        $certificate->delete();

        return redirect()->route('certificate-types.index')->with('message', 'Certificate deleted successfully!');
    }
  
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $query = CertificateType::query();

        if ($search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $types = $query->orderBy('id', 'desc')
                    ->paginate(10)
                    ->withQueryString();

        if ($request->ajax()) {
            return view('certificate.table', compact('types'))->render();
        }

        return view('certificate.index', compact('types', 'search'));
    }
}


