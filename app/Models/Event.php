<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon; // Import Carbon for datetime casting

/**
 * @author Camilo Polanía
 * @date 2024-01-15
 * Descripción: Modelo para gestión de eventos del sistema EventPass
 */
class Event extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'capacidad',
        'estado',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'capacidad' => 'integer',
    ];

    /**
     * GETTERS - Obtener valores de atributos
     */

    /**
     * Obtener el nombre del evento
     *
     * @return string
     */
    public function getNombre(): string
    {
        return $this->attributes['nombre'];
    }

    /**
     * Obtener la descripción del evento
     *
     * @return string
     */
    public function getDescripcion(): string
    {
        return $this->attributes['descripcion'];
    }

    /**
     * Obtener la fecha de inicio del evento
     *
     * @return Carbon
     */
    public function getFechaInicio(): Carbon
    {
        return $this->fecha_inicio;
    }

    /**
     * Obtener la fecha de fin del evento
     *
     * @return Carbon
     */
    public function getFechaFin(): Carbon
    {
        return $this->fecha_fin;
    }

    /**
     * Obtener la capacidad del evento
     *
     * @return int
     */
    public function getCapacidad(): int
    {
        return $this->attributes['capacidad'];
    }

    /**
     * Obtener el estado del evento
     *
     * @return string
     */
    public function getEstado(): string
    {
        return $this->attributes['estado'];
    }

    /**
     * SETTERS - Establecer valores de atributos
     */

    /**
     * Establecer el nombre del evento
     *
     * @param string $nombre
     * @return void
     */
    public function setNombre(string $nombre): void
    {
        $this->attributes['nombre'] = trim($nombre);
    }

    /**
     * Establecer la descripción del evento
     *
     * @param string $descripcion
     * @return void
     */
    public function setDescripcion(string $descripcion): void
    {
        $this->attributes['descripcion'] = trim($descripcion);
    }

    /**
     * Establecer la fecha de inicio del evento
     *
     * @param Carbon|string $fechaInicio
     * @return void
     */
    public function setFechaInicio($fechaInicio): void
    {
        $this->attributes['fecha_inicio'] = $fechaInicio instanceof Carbon ? $fechaInicio : Carbon::parse($fechaInicio);
    }

    /**
     * Establecer la fecha de fin del evento
     *
     * @param Carbon|string $fechaFin
     * @return void
     */
    public function setFechaFin($fechaFin): void
    {
        $this->attributes['fecha_fin'] = $fechaFin instanceof Carbon ? $fechaFin : Carbon::parse($fechaFin);
    }

    /**
     * Establecer la capacidad del evento
     *
     * @param int $capacidad
     * @return void
     */
    public function setCapacidad(int $capacidad): void
    {
        $this->attributes['capacidad'] = max(1, $capacidad);
    }

    /**
     * Establecer el estado del evento
     *
     * @param string $estado
     * @return void
     */
    public function setEstado(string $estado): void
    {
        $estadosValidos = ['borrador', 'publicado', 'cancelado', 'completado'];
        if (in_array($estado, $estadosValidos)) {
            $this->attributes['estado'] = $estado;
        }
    }

    /**
     * RELACIONES
     */

    /**
     * Relación: Un evento tiene muchos tickets
     *
     * @return HasMany
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'evento_id');
    }

    /**
     * Relación: Un evento tiene muchas entradas en la lista de espera
     *
     * @return HasMany
     */
    public function entradasListaEspera(): HasMany
    {
        return $this->hasMany(WaitlistEntry::class, 'evento_id');
    }

    /**
     * MÉTODOS DE NEGOCIO
     */

    /**
     * Obtener la capacidad disponible del evento
     *
     * @return int
     */
    public function capacidadDisponible(): int
    {
        // Calcular capacidad disponible basada en tickets vendidos
        $ticketsVendidos = $this->tickets()->sum('cantidad_vendida');
        return max(0, $this->capacidad - $ticketsVendidos);
    }

    /**
     * Verificar si el evento está agotado
     *
     * @return bool
     */
    public function estaAgotado(): bool
    {
        return $this->capacidadDisponible() <= 0;
    }

    /**
     * Generar reporte de ventas del evento
     *
     * @return array
     */
    public function reporteVentas(): array
    {
        $ticketsVendidos = $this->tickets()->sum('cantidad_vendida');
        $ingresosTotales = $this->tickets()->get()->sum(function ($ticket) {
            return $ticket->cantidad_vendida * $ticket->precio;
        });

        return [
            'capacidad_total' => $this->capacidad,
            'capacidad_disponible' => $this->capacidadDisponible(),
            'tickets_vendidos' => $ticketsVendidos,
            'porcentaje_ocupacion' => $this->capacidad > 0 ? round(($ticketsVendidos / $this->capacidad) * 100, 2) : 0,
            'ingresos_totales' => $ingresosTotales
        ];
    }

    /**
     * SCOPES - Consultas predefinidas
     */

    /**
     * Scope para eventos publicados
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublicados($query)
    {
        return $query->where('estado', 'publicado');
    }

    /**
     * Scope para eventos disponibles
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'publicado')
                    ->where('fecha_inicio', '>', now());
    }
}
