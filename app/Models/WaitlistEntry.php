<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @author Camilo Polanía
 * @date 2024-01-15
 * Descripción: Modelo para gestión de lista de espera de eventos
 */
class WaitlistEntry extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'usuario_id',
        'evento_id',
        'estado',
        'fecha_notificacion',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_notificacion' => 'datetime',
    ];

    /**
     * GETTERS - Obtener valores de atributos
     */

    /**
     * Obtener el ID del usuario
     *
     * @return int
     */
    public function getUsuarioId(): int
    {
        return $this->attributes['usuario_id'];
    }

    /**
     * Obtener el ID del evento
     *
     * @return int
     */
    public function getEventoId(): int
    {
        return $this->attributes['evento_id'];
    }

    /**
     * Obtener el estado de la entrada
     *
     * @return string
     */
    public function getEstado(): string
    {
        return $this->attributes['estado'];
    }

    /**
     * Obtener la fecha de notificación
     *
     * @return \Carbon\Carbon|null
     */
    public function getFechaNotificacion(): ?\Carbon\Carbon
    {
        return $this->attributes['fecha_notificacion'];
    }

    /**
     * SETTERS - Establecer valores de atributos
     */

    /**
     * Establecer el ID del usuario
     *
     * @param int $usuarioId
     * @return void
     */
    public function setUsuarioId(int $usuarioId): void
    {
        $this->attributes['usuario_id'] = $usuarioId;
    }

    /**
     * Establecer el ID del evento
     *
     * @param int $eventoId
     * @return void
     */
    public function setEventoId(int $eventoId): void
    {
        $this->attributes['evento_id'] = $eventoId;
    }

    /**
     * Establecer el estado de la entrada
     *
     * @param string $estado
     * @return void
     */
    public function setEstado(string $estado): void
    {
        $estadosValidos = ['esperando', 'notificado', 'convertido', 'expirado'];
        if (in_array($estado, $estadosValidos)) {
            $this->attributes['estado'] = $estado;
        }
    }

    /**
     * Establecer la fecha de notificación
     *
     * @param \Carbon\Carbon|null $fechaNotificacion
     * @return void
     */
    public function setFechaNotificacion(?\Carbon\Carbon $fechaNotificacion): void
    {
        $this->attributes['fecha_notificacion'] = $fechaNotificacion;
    }

    /**
     * RELACIONES
     */

    /**
     * Relación: Una entrada de lista de espera pertenece a un evento
     *
     * @return BelongsTo
     */
    public function evento(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'evento_id');
    }

    /**
     * Relación: Una entrada de lista de espera pertenece a un usuario
     *
     * @return BelongsTo
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * MÉTODOS DE NEGOCIO
     */

    /**
     * Notificar al usuario sobre disponibilidad
     *
     * @return bool
     */
    public function notificarUsuario(): bool
    {
        if ($this->estado === 'esperando' && $this->usuario) {
            // Marcar como notificado
            $this->update([
                'estado' => 'notificado',
                'fecha_notificacion' => now(),
            ]);

            // TODO: Implementar notificación con colas

            return true;
        }

        return false;
    }

    /**
     * Marcar como convertido (cuando el usuario compra ticket)
     *
     * @return bool
     */
    public function marcarComoConvertido(): bool
    {
        if ($this->estado === 'notificado') {
            $this->estado = 'convertido';
            return $this->save();
        }
        return false;
    }

    /**
     * SCOPES - Consultas predefinidas
     */

    /**
     * Scope para entradas esperando
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEsperando($query)
    {
        return $query->where('estado', 'esperando');
    }

    /**
     * Scope para entradas notificadas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotificadas($query)
    {
        return $query->where('estado', 'notificado');
    }

    /**
     * Scope para entradas de un evento específico
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $eventoId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDelEvento($query, int $eventoId)
    {
        return $query->where('evento_id', $eventoId);
    }
}
