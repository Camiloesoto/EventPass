@extends('layouts.app')

@section('title', 'Código QR - Ticket #' . $ticket->getCode())

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-qrcode"></i> Código QR del Ticket
                    </h4>
                </div>
                <div class="card-body text-center">
                    <!-- QR Code Display -->
                    <div class="mb-4">
                        <div style="width: 200px; height: 200px; margin: 0 auto;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(json_encode([
                                'ticket_id' => $ticket->getId(),
                                'user_id' => $ticket->getUserId(),
                                'event' => $ticket->orderItem->ticketType->event->getName(),
                                'code' => $ticket->getCode(),
                                'hash' => $ticket->getQrCodeHash(),
                                'timestamp' => now()->toISOString()
                            ])) }}" 
                                 alt="QR Code" 
                                 class="img-fluid border rounded">
                        </div>
                    </div>
                    
                    <!-- Ticket Information -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h5 class="card-title">{{ $ticket->orderItem->ticketType->event->getName() }}</h5>
                            <p class="card-text">
                                <strong>Ticket:</strong> {{ $ticket->getCode() }}<br>
                                <strong>Tipo:</strong> {{ $ticket->orderItem->ticketType->getName() }}<br>
                                <strong>Precio:</strong> ${{ number_format($ticket->orderItem->getUnitPrice(), 2) }}<br>
                                <strong>Estado:</strong> 
                                <span class="badge badge-{{ $ticket->getStatus()->value === 'issued' ? 'success' : 'info' }}">
                                    {{ ucfirst($ticket->getStatus()->value) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('tickets.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Volver a Mis Tickets
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="fas fa-print"></i> Imprimir QR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code is generated server-side using QR Server API -->

<style>
@media print {
    .btn, .card-header {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
