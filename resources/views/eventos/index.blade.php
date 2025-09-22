<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos - EventPass</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .event-card { border: 1px solid #ddd; padding: 20px; margin: 10px 0; border-radius: 8px; }
        .event-title { color: #333; font-size: 24px; margin-bottom: 10px; }
        .event-description { color: #666; margin-bottom: 15px; }
        .event-info { display: flex; gap: 20px; margin-bottom: 10px; }
        .event-status { padding: 5px 10px; border-radius: 4px; color: white; font-weight: bold; }
        .status-publicado { background-color: #28a745; }
        .status-borrador { background-color: #6c757d; }
        .status-cancelado { background-color: #dc3545; }
        .status-completado { background-color: #17a2b8; }
        .btn { padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 5px; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-info { background-color: #17a2b8; color: white; }
        .btn:hover { opacity: 0.8; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Eventos - EventPass</h1>
        
        <div style="margin-bottom: 20px;">
            <a href="{{ route('eventos.create') }}" class="btn btn-success">Crear Nuevo Evento</a>
            <a href="{{ route('eventos.disponibles') }}" class="btn btn-primary">Ver Eventos Disponibles</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($eventos->count() > 0)
            <h2>Lista de Eventos</h2>
            @foreach($eventos as $evento)
                <div class="event-card">
                    <h3 class="event-title">{{ $evento->getNombre() }}</h3>
                    <p class="event-description">{{ $evento->getDescripcion() }}</p>
                    
                    <div class="event-info">
                        <strong>Inicio:</strong> {{ $evento->getFechaInicio()->format('d/m/Y H:i') }}
                        <strong>Fin:</strong> {{ $evento->getFechaFin()->format('d/m/Y H:i') }}
                        <strong>Capacidad:</strong> {{ $evento->getCapacidad() }}
                        <strong>Disponible:</strong> {{ $evento->capacidadDisponible() }}
                        <span class="event-status status-{{ $evento->getEstado() }}">
                            {{ ucfirst($evento->getEstado()) }}
                        </span>
                    </div>

                    <div>
                        <a href="{{ route('eventos.show', $evento) }}" class="btn btn-primary">Ver Detalles</a>
                        <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-primary">Editar</a>
                        <a href="{{ route('eventos.reporte', $evento) }}" class="btn btn-info">Reporte</a>
                        @if(!$evento->trashed())
                            <form method="POST" action="{{ route('eventos.destroy', $evento) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar este evento?')">
                                    Eliminar
                                </button>
                            </form>
                        @else
                            <span style="color: #dc3545; font-weight: bold;">Eliminado</span>
                        @endif
                    </div>
                </div>
            @endforeach

            {{ $eventos->links() }}
        @else
            <p>No hay eventos registrados.</p>
        @endif
    </div>
</body>
</html>
