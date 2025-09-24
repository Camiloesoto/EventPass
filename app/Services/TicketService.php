<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Jobs\GenerateTicketArtifacts;
use App\Models\Ticket;
use App\Models\TicketCheckin;
use DomainException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketService
{
    public function issue(int $orderItemId, ?int $userId = null): Ticket
    {
        $ticket = Ticket::create([
            'order_item_id' => $orderItemId,
            'user_id' => $userId,
            'qr_code_hash' => Str::lower(hash('sha256', uniqid((string) $orderItemId, true))),
            'pdf_url' => '#',
            'status' => TicketStatus::issued,
        ]);

        GenerateTicketArtifacts::dispatch($ticket->id);

        return $ticket;
    }

    public function transfer(Ticket $ticket, ?int $toUserId): Ticket
    {
        if ($ticket->isRedeemed()) {
            throw new DomainException('Ticket already redeemed.');
        }

        $ticket->update([
            'user_id' => $toUserId,
            'status' => TicketStatus::transferred,
        ]);

        return $ticket->fresh();
    }

    public function revoke(Ticket $ticket): Ticket
    {
        if ($ticket->status === TicketStatus::cancelled) {
            return $ticket;
        }

        $ticket->update([
            'status' => TicketStatus::cancelled,
        ]);

        return $ticket->fresh();
    }

    public function redeemByHash(string $qrCodeHash, ?int $scannerId = null, ?string $device = null, ?string $location = null): Ticket
    {
        return $this->redeemByIdentifier($qrCodeHash, $scannerId, $device, $location);
    }

    public function redeemByIdentifier(string $identifier, ?int $scannerId = null, ?string $device = null, ?string $location = null): Ticket
    {
        $identifier = trim($identifier);

        return DB::transaction(function () use ($identifier, $scannerId, $device, $location) {
            $ticket = $this->findTicketForRedemption($identifier);
            if (! $ticket) {
                throw new DomainException('Ticket not found.');
            }

            if ($ticket->status === TicketStatus::cancelled) {
                throw new DomainException('Ticket revoked.');
            }

            if ($ticket->isRedeemed()) {
                return $ticket;
            }

            $ticket->update([
                'status' => TicketStatus::redeemed,
                'redeemed_at' => now(),
            ]);

            TicketCheckin::create([
                'ticket_id' => $ticket->id,
                'scanned_by_user_id' => $scannerId,
                'scanned_at' => Carbon::now(),
                'device' => $device,
                'location' => $location,
            ]);

            return $ticket->fresh();
        });
    }

    private function findTicketForRedemption(string $identifier): ?Ticket
    {
        if ($this->looksLikeHash($identifier)) {
            return Ticket::lockForUpdate()->where('qr_code_hash', strtolower($identifier))->first();
        }

        if (ctype_digit($identifier)) {
            return Ticket::lockForUpdate()->find((int) $identifier);
        }

        // Fallback: treat as human-friendly ticket code (e.g., ABC123-...)
        $upper = strtoupper($identifier);
        if (preg_match('/^[A-Z0-9\-]{6,64}$/', $upper)) {
            return Ticket::lockForUpdate()->where('code', $upper)->first();
        }

        return null;
    }

    private function looksLikeHash(string $value): bool
    {
        return strlen($value) === 64 && ctype_xdigit($value);
    }
}
