<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        // Placeholder - implementar lógica de verificación de email
        return redirect()->route('dashboard')->with('status', 'Email verified!');
    }
}