<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfirmablePasswordController extends Controller
{
    public function show(Request $request)
    {
        return view('auth.confirm-password');
    }

    public function store(Request $request)
    {
        // Placeholder - implementar lógica de confirmación de contraseña
        return redirect()->intended(route('dashboard'));
    }
}