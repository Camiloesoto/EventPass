<?php

namespace App\Http\Controllers;

use App\Enums\WaitlistStatus;
use App\Models\Event;
use App\Models\WaitlistEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    public function store(Request $request, Event $event): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'Unauthorized'], 401)
                : redirect()->route('login');
        }

        $entry = WaitlistEntry::firstOrCreate(
            ['user_id' => $user->id, 'event_id' => $event->id],
            ['status' => WaitlistStatus::waiting]
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'entry' => $entry], 201);
        }

        return back()->with('status', 'You have been added to the waitlist.');
    }
}
