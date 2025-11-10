<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        // User create karo
        app(CreateNewUser::class)->create($request->all());

        // Auto login skip karo, login page redirect karo
        return redirect()->route('login')->with('status', 'Registration successful! Please login.');
    }
}
