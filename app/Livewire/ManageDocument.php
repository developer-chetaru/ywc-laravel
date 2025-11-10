<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class ManageDocument extends Component
{
    public $iframeUrl;

    public function mount()
    {
        // Default email (Next.js wale code jaisa)
       $email = auth()->check() ? auth()->user()->email : 'ellen+test101@crewdentials.com';

        // API request
        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer c8022bede84da295a6448c1277391a5',
        ])->post('https://crewdentials-api.onrender.com/api/v1/profilePreview', [
            'crewEmailAddress' => $email,
            'dashboardMode'    => true,
        ]);

        $data = $response->json();

        if (isset($data['publicProfileIframeUrl'])) {
            $this->iframeUrl = $data['publicProfileIframeUrl'];
        } else {
            $this->iframeUrl = null;
        }
    }

    public function render()
    {
        return view('livewire.manage-document')->layout('layouts.app');
    }
}
