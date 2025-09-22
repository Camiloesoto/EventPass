<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * @author Camilo Polanía
 * @date 2024-01-15
 * Descripción: Controlador para gestión de eventos
 */
class EventController extends Controller
{
    /**
     * Mostrar lista de eventos
     *
     * @return View
     */
    public function index(): JsonResponse
    {
        $eventos = Event::latest()->get();
        
        return response()->json([
            'success' => true,
            'data' => $eventos->map(function ($evento) {
                return [
                    'id' => $evento->id,
                    'nombre' => $evento->getNombre(),
                    'descripcion' => $evento->getDescripcion(),
                    'fecha_inicio' => $evento->getFechaInicio()->format('Y-m-d H:i:s'),
                    'fecha_fin' => $evento->getFechaFin()->format('Y-m-d H:i:s'),
                    'capacidad' => $evento->getCapacidad(),
                    'estado' => $evento->getEstado(),
                    'capacidad_disponible' => $evento->capacidadDisponible(),
                    'esta_agotado' => $evento->estaAgotado(),
                    'created_at' => $evento->created_at,
                    'updated_at' => $evento->updated_at,
                ];
            }),
            'total' => $eventos->count()
        ]);
    }

    /**
     * Mostrar eventos disponibles
     *
     * @return View
     */
    public function disponibles(): View
    {
        $eventos = Event::disponibles()->latest('fecha_inicio')->paginate(10);
        
        return view('eventos.disponibles', compact('eventos'));
    }

    /**
     * Mostrar formulario de creación
     *
     * @return View
     */
    public function create(): View
    {
        return view('eventos.create');
    }

    /**
     * Crear nuevo evento
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255|min:3',
            'descripcion' => 'required|string|min:10|max:2000',
            'fecha_inicio' => 'required|date|after:now',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'capacidad' => 'required|integer|min:1|max:10000',
            'estado' => 'sometimes|string|in:borrador,publicado,cancelado,completado'
        ]);

        $datos = $request->all();
        $evento = Event::create($datos);

        return response()->json([
            'success' => true,
            'message' => 'Evento creado exitosamente.',
            'data' => [
                'id' => $evento->id,
                'nombre' => $evento->getNombre(),
                'descripcion' => $evento->getDescripcion(),
                'fecha_inicio' => $evento->getFechaInicio()->format('Y-m-d H:i:s'),
                'fecha_fin' => $evento->getFechaFin()->format('Y-m-d H:i:s'),
                'capacidad' => $evento->getCapacidad(),
                'estado' => $evento->getEstado(),
            ]
        ], 201);
    }

    /**
     * Mostrar evento específico
     *
     * @param Event $evento
     * @return View
     */
    public function show(Event $evento): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $evento->id,
                'nombre' => $evento->getNombre(),
                'descripcion' => $evento->getDescripcion(),
                'fecha_inicio' => $evento->getFechaInicio()->format('Y-m-d H:i:s'),
                'fecha_fin' => $evento->getFechaFin()->format('Y-m-d H:i:s'),
                'capacidad' => $evento->getCapacidad(),
                'estado' => $evento->getEstado(),
                'capacidad_disponible' => $evento->capacidadDisponible(),
                'esta_agotado' => $evento->estaAgotado(),
                'reporte_ventas' => $evento->reporteVentas(),
                'created_at' => $evento->created_at,
                'updated_at' => $evento->updated_at,
            ]
        ]);
    }

    /**
     * Mostrar formulario de edición
     *
     * @param Event $evento
     * @return View
     */
    public function edit(Event $evento): View
    {
        return view('eventos.edit', compact('evento'));
    }

    /**
     * Actualizar evento
     *
     * @param Request $request
     * @param Event $evento
     * @return RedirectResponse
     */
    public function update(Request $request, Event $evento): RedirectResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255|min:3',
            'descripcion' => 'required|string|min:10|max:2000',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'capacidad' => 'required|integer|min:1|max:10000',
            'estado' => 'sometimes|string|in:borrador,publicado,cancelado,completado'
        ]);

        $evento->update($request->all());

        return redirect()
            ->route('eventos.show', $evento)
            ->with('success', 'Evento actualizado exitosamente.');
    }

    /**
     * Eliminar evento (soft delete)
     *
     * @param Event $evento
     * @return RedirectResponse
     */
    public function destroy(Event $evento): RedirectResponse
    {
        $evento->delete();

        return redirect()
            ->route('eventos.index')
            ->with('success', 'Evento eliminado exitosamente.');
    }

    /**
     * Mostrar reporte de ventas del evento
     *
     * @param Event $evento
     * @return View
     */
    public function reporte(Event $evento): View
    {
        $reporte = $evento->reporteVentas();
        
        return view('eventos.reporte', compact('evento', 'reporte'));
    }
}
