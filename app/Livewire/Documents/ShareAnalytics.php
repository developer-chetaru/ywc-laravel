<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use App\Models\DocumentShare;
use App\Models\ShareAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShareAnalytics extends Component
{
    public $analytics = [];
    public $recentActivity = [];
    public $loading = true;

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        $this->loading = true;
        
        $user = Auth::user();
        $shares = DocumentShare::where('user_id', $user->id)
            ->with('documents')
            ->get();

        $this->analytics = [
            'total_shares' => $shares->count(),
            'active_shares' => $shares->where('is_active', true)->count(),
            'expired_shares' => $shares->where('is_active', false)->count(),
            'total_views' => $shares->sum('access_count'),
            'total_downloads' => $shares->sum('download_count'),
            'shares_by_month' => $shares->groupBy(function($share) {
                return $share->created_at->format('Y-m');
            })->map(function($group) {
                return $group->count();
            }),
        ];

        // Get top shared documents
        $this->analytics['top_shared_documents'] = \DB::table('document_share_documents')
            ->join('documents', 'document_share_documents.document_id', '=', 'documents.id')
            ->join('document_shares', 'document_share_documents.document_share_id', '=', 'document_shares.id')
            ->where('document_shares.user_id', $user->id)
            ->select('documents.id', 'documents.document_name', \DB::raw('COUNT(*) as share_count'))
            ->groupBy('documents.id', 'documents.document_name')
            ->orderBy('share_count', 'desc')
            ->limit(10)
            ->get();

        // Get recent activity
        $this->recentActivity = ShareAuditLog::where('share_type', 'document')
            ->whereIn('shareable_id', $shares->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $this->loading = false;
    }

    public function render()
    {
        return view('livewire.documents.share-analytics', [
            'analytics' => $this->analytics,
            'recentActivity' => $this->recentActivity,
        ])->layout('layouts.app-laravel');
    }
}
