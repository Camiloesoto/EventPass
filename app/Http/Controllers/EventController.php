<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('venue')
            ->orderBy('start_time')
            ->paginate(12);

        return view('events.index', compact('events'));
    }

    public function create()
    {
        $venues = Venue::all();
        return view('events.create', compact('venues'));
    }

    public function store(StoreEventRequest $request)
    {
        try {
            $event = Event::create($request->validated());
            
            Log::info('Event created', [
                'event_id' => $event->getId(),
                'event_name' => $event->getName(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('events.show', $event)
                ->with('success', 'Event created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating event', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->withInput()
                ->with('error', 'Error creating event. Please try again.');
        }
    }

    public function show(Event $event)
    {
        $event->load(['venue', 'ticketTypes']);
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $venues = Venue::all();
        return view('events.edit', compact('event', 'venues'));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        try {
            $event->update($request->validated());
            
            Log::info('Event updated', [
                'event_id' => $event->getId(),
                'event_name' => $event->getName(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('events.show', $event)
                ->with('success', 'Event updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating event', [
                'event_id' => $event->getId(),
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->withInput()
                ->with('error', 'Error updating event. Please try again.');
        }
    }

    public function destroy(Event $event)
    {
        try {
            $eventName = $event->getName();
            $event->delete();
            
            Log::info('Event deleted', [
                'event_id' => $event->getId(),
                'event_name' => $eventName,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('events.index')
                ->with('success', 'Event deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting event', [
                'event_id' => $event->getId(),
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->with('error', 'Error deleting event. Please try again.');
        }
    }
}