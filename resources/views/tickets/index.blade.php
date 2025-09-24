@extends('layouts.app')

@section('title', 'Mis Tickets')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Mis Tickets</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>
</div>

@if($tickets->count() > 0)
    <div class="row">
        @foreach($tickets as $ticket)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card ticket-card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Ticket #{{ $ticket->code }}</h6>
                            <span class="badge 
                                @if($ticket->getStatus() === 'issued') badge-success
                                @elseif($ticket->getStatus() === 'redeemed') badge-info
                                @elseif($ticket->getStatus() === 'transferred') badge-warning
                                @else badge-danger
                                @endif">
                                {{ ucfirst($ticket->getStatus()) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Evento</small>
                            <p class="mb-0">{{ $ticket->orderItem->ticketType->event->getName() }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">Tipo de Ticket</small>
                            <p class="mb-0">{{ $ticket->orderItem->ticketType->name }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">Fecha del Evento</small>
                            <p class="mb-0">{{ $ticket->orderItem->ticketType->event->getStartTime()->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        @if($ticket->redeemed_at)
                            <div class="mb-3">
                                <small class="text-muted">Canjeado el</small>
                                <p class="mb-0">{{ $ticket->redeemed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif
                        
                        <div class="d-grid gap-2">
                            @if($ticket->pdf_url)
                                <a href="{{ route('tickets.download', $ticket) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-download"></i> Descargar PDF
                                </a>
                            @endif
                            
                            <a href="{{ route('tickets.qr', $ticket) }}" class="btn btn-outline-info" target="_blank">
                                <i class="fas fa-qrcode"></i> Ver Código QR
                            </a>
                            
                            @if($ticket->getStatus() === 'issued')
                                <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#transferModal{{ $ticket->id }}">
                                    <i class="fas fa-exchange-alt"></i> Transferir
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal de Transferencia -->
            @if($ticket->getStatus() === 'issued')
                <div class="modal fade" id="transferModal{{ $ticket->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Transferir Ticket</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="{{ route('tickets.transfer', $ticket) }}">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="email{{ $ticket->id }}" class="form-label">Email del destinatario</label>
                                        <input type="email" class="form-control" id="email{{ $ticket->id }}" name="to_email" required>
                                        <div class="form-text">El destinatario debe tener una cuenta registrada.</div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-warning">Transferir Ticket</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@else
    <div class="text-center py-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">No tienes tickets</h5>
                <p class="card-text text-muted">
                    Cuando compres tickets para eventos, aparecerán aquí.
                </p>
                <a href="{{ route('events.index') }}" class="btn btn-primary">
                    <i class="fas fa-calendar"></i> Ver Eventos
                </a>
            </div>
        </div>
    </div>
@endif
@endsection
