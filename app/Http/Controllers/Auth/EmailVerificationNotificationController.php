<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request)
    {
        // Placeholder - implementar lógica de verificación de email
        return back()->with('status', 'Verification link sent!');
    }
}