<?php

namespace App\Http\Controllers;

use App\Models\Waitlist;
use Illuminate\Http\Request;

class WaitlistAdminController extends Controller
{
    public function index(Request $request)
    {
        // Extra safety: only allow super admins to access waitlist admin
        if (! auth()->user() || ! auth()->user()->hasRole('super_admin')) {
            abort(403);
        }

        $status = $request->get('status', 'all');

        $entries = Waitlist::when($status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.waitlist', [
            'entries' => $entries,
            'status' => $status,
        ]);
    }

    public function update(Request $request, Waitlist $waitlist)
    {
        // Extra safety: only allow super admins to update waitlist entries
        if (! auth()->user() || ! auth()->user()->hasRole('super_admin')) {
            abort(403);
        }

        $data = $request->validate([
            'status' => 'required|in:pending,approved,invited',
            'notes' => 'nullable|string|max:500',
        ]);

        $waitlist->status = $data['status'];
        $waitlist->notes = $data['notes'] ?? $waitlist->notes;

        if ($data['status'] === 'approved' && ! $waitlist->approved_at) {
            $waitlist->approved_at = now();
        }

        if ($data['status'] === 'invited' && ! $waitlist->invited_at) {
            $waitlist->invited_at = now();
        }

        $waitlist->save();

        return back()->with('success', 'Waitlist entry updated.');
    }
}
