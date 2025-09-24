<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Venue;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EventAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Log de acceso al listado de eventos
        Log::info('Admin accedió al listado de eventos', [
            'admin_id' => auth()->id(),
            'admin_email' => auth()->user()->email,
            'filters' => $request->all()
        ]);

        $query = Event::with(['venue', 'ticketTypes']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $events = $query->latest('created_at')->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Log::info('Admin accedió al formulario de creación de evento', [
            'admin_id' => auth()->id(),
            'admin_email' => auth()->user()->email
        ]);

        $venues = Venue::orderBy('name')->get();
        return view('admin.events.create', compact('venues'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        try {
            DB::beginTransaction();

            $event = Event::create($request->validated());

            Log::info('Admin creó un nuevo evento', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'event_id' => $event->id,
                'event_name' => $event->name,
                'event_data' => $request->validated()
            ]);

            DB::commit();

            return redirect()->route('admin.events.index')
                ->with('success', 'Event created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear evento desde admin', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'error' => $e->getMessage(),
                'event_data' => $request->validated()
            ]);

            return back()->withInput()
                ->with('error', 'Error al crear el evento. Inténtalo de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        Log::info('Admin visualizó evento', [
            'admin_id' => auth()->id(),
            'admin_email' => auth()->user()->email,
            'event_id' => $event->id,
            'event_name' => $event->name
        ]);

        $event->load(['venue', 'ticketTypes', 'waitlistEntries.user']);
        
        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        Log::info('Admin accedió al formulario de edición de evento', [
            'admin_id' => auth()->id(),
            'admin_email' => auth()->user()->email,
            'event_id' => $event->id,
            'event_name' => $event->name
        ]);

        $venues = Venue::orderBy('name')->get();
        return view('admin.events.edit', compact('event', 'venues'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        try {
            DB::beginTransaction();

            $oldData = $event->toArray();
            $event->update($request->validated());

            Log::info('Admin actualizó evento', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'event_id' => $event->id,
                'event_name' => $event->name,
                'old_data' => $oldData,
                'new_data' => $request->validated()
            ]);

            DB::commit();

            return redirect()->route('admin.events.index')
                ->with('success', 'Event updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al actualizar evento desde admin', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'event_id' => $event->id,
                'error' => $e->getMessage(),
                'event_data' => $request->validated()
            ]);

            return back()->withInput()
                ->with('error', 'Error al actualizar el evento. Inténtalo de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        try {
            DB::beginTransaction();

            $eventData = $event->toArray();
            $event->delete();

            Log::info('Admin eliminó evento', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'event_id' => $event->id,
                'event_name' => $eventData['name'],
                'event_data' => $eventData
            ]);

            DB::commit();

            return redirect()->route('admin.events.index')
                ->with('success', 'Event deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al eliminar evento desde admin', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'event_id' => $event->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Error al eliminar el evento. Inténtalo de nuevo.');
        }
    }
}