<?php
namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::query()->latest('start_time')->paginate(12);
        return view('events.index', ['events' => $events]);
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $event = Event::create($request->validated());
        return redirect()->route('events.show', ['event' => $event]);
    }

    public function show(Event $event): View
    {
        return view('events.show', ['event' => $event]);
    }
}
