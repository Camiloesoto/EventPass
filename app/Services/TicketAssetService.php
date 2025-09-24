<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;

class TicketAssetService
{
    public function generateFor(Ticket $ticket): void
    {
        $disk = Storage::disk('local');
        $directory = 'tickets/' . $ticket->getKey();

        $disk->makeDirectory($directory);

        $qrPath = $directory . '/qr.txt';
        $pdfPath = $directory . '/ticket.pdf';

        $disk->put($qrPath, $this->buildQrContents($ticket));
        $disk->put($pdfPath, $this->buildPdfContents($ticket));

        $ticket->forceFill([
            'pdf_url' => $pdfPath,
        ])->save();
    }

    private function buildQrContents(Ticket $ticket): string
    {
        return 'QR:' . $ticket->qr_code_hash;
    }

    private function buildPdfContents(Ticket $ticket): string
    {
        $lines = [
            'Ticket PDF',
            '============',
            'Code: ' . $ticket->code,
            'Hash: ' . $ticket->qr_code_hash,
            'Issued for user: ' . ($ticket->user_id ?? 'unassigned'),
        ];

        return implode("\n", $lines);
    }
}
