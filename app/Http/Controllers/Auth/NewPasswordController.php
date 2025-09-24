<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewPasswordController extends Controller
{
    public function create(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    public function store(Request $request)
    {
        // Placeholder - implementar lógica de reset de contraseña
        return redirect()->route('login')->with('status', 'Password reset successfully!');
    }
}