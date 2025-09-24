<?php // app/Http/Controllers/EventController.php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::query()->latest('start_time')->paginate(12);
        return view('events.index', ['events' => $events]);
    }

    public function create(): View
    {
        return view('events.create');
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

    public function edit(Event $event): View
    {
        return view('events.edit', ['event' => $event]);
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $event->update($request->validated());
        return redirect()->route('events.show', ['event' => $event]);
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();
        return redirect()->route('events.index');
    }
}
