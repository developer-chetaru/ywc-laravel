<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManageDocumentController extends Controller
{
    public function manageDocument()
    {
        return view('manage-document.index');
    }
   public function mentalHealth()
    {
        return redirect()->route('main-dashboard');
    }

    public function documents()
    {
        return redirect()->route('main-dashboard');
    }

    public function forums()
    {
        return redirect()->route('main-dashboard');
    }

    public function training()
    {
        return redirect()->route('main-dashboard');
    }

    public function weather()
    {
        return redirect()->route('main-dashboard');
    }

    public function review()
    {
        return redirect()->route('main-dashboard');
    }

    public function itinerarySystem()
    {
        return redirect()->route('main-dashboard');
    }
}
