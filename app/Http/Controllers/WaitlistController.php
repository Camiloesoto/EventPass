<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\WaitlistEntry;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * @author Camilo Polanía
 * @date 2024-01-15
 * Descripción: Controlador para gestión de lista de espera
 */
class WaitlistController extends Controller
{
    /**
     * Agregar usuario a la lista de espera
     *
     * @param Request $request
     * @param Event $evento
     * @return RedirectResponse|JsonResponse
     */
    public function agregar(Request $request, Event $evento): RedirectResponse|JsonResponse
    {
        $request->validate([
            'usuario_id' => 'required|integer|exists:users,id'
        ]);

        // Verificar si el usuario ya está en la lista de espera
        $entradaExistente = WaitlistEntry::where('evento_id', $evento->id)
            ->where('usuario_id', $request->usuario_id)
            ->first();

        if ($entradaExistente) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Ya estás en la lista de espera para este evento'], 422);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Ya estás en la lista de espera para este evento');
        }

        // Verificar si el evento tiene capacidad disponible
        if (!$evento->estaAgotado()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'El evento aún tiene capacidad disponible'], 422);
            }
            
            return redirect()
                ->back()
                ->with('error', 'El evento aún tiene capacidad disponible');
        }

        // Agregar a la lista de espera
        WaitlistEntry::create([
            'usuario_id' => $request->usuario_id,
            'evento_id' => $evento->id,
            'estado' => 'esperando'
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Te has unido a la lista de espera exitosamente']);
        }

        return redirect()
            ->back()
            ->with('success', 'Te has unido a la lista de espera exitosamente.');
    }

    /**
     * Remover usuario de la lista de espera
     *
     * @param Request $request
     * @param Event $evento
     * @return RedirectResponse|JsonResponse
     */
    public function remover(Request $request, Event $evento): RedirectResponse|JsonResponse
    {
        $request->validate([
            'usuario_id' => 'required|integer|exists:users,id'
        ]);

        $entrada = WaitlistEntry::where('evento_id', $evento->id)
            ->where('usuario_id', $request->usuario_id)
            ->first();

        if (!$entrada) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No estás en la lista de espera para este evento'], 404);
            }
            
            return redirect()
                ->back()
                ->with('error', 'No estás en la lista de espera para este evento');
        }

        $entrada->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Te has removido de la lista de espera exitosamente']);
        }

        return redirect()
            ->back()
            ->with('success', 'Te has removido de la lista de espera exitosamente.');
    }

    /**
     * Mostrar lista de espera de un evento
     *
     * @param Event $evento
     * @return View
     */
    public function mostrar(Event $evento): View
    {
        $entradas = WaitlistEntry::where('evento_id', $evento->id)
            ->with('usuario')
            ->orderBy('created_at')
            ->paginate(20);

        return view('lista-espera.mostrar', compact('evento', 'entradas'));
    }

    /**
     * Notificar usuarios en lista de espera
     *
     * @param Event $evento
     * @return RedirectResponse
     */
    public function notificar(Event $evento): RedirectResponse
    {
        $entradasEsperando = WaitlistEntry::where('evento_id', $evento->id)
            ->where('estado', 'esperando')
            ->limit($evento->capacidadDisponible())
            ->get();

        $notificados = 0;
        foreach ($entradasEsperando as $entrada) {
            if ($entrada->notificarUsuario()) {
                $notificados++;
            }
        }

        return redirect()
            ->back()
            ->with('success', "Se notificaron {$notificados} usuarios de la lista de espera.");
    }
}
