<?php
namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketCheckin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use DomainException;

class TicketService
{
    public function issue(?int $userId, array $meta = []): Ticket
    {
        return Ticket::create([
            'user_id' => $userId,
            'meta'    => $meta,
            'status'  => Ticket::STATUS_ISSUED,
        ]);
    }

    public function transfer(Ticket $ticket, ?int $toUserId): Ticket
    {
        if ($ticket->isRedeemed()) throw new DomainException('Ticket already redeemed.');
        $ticket->update([
            'user_id' => $toUserId,
            'status'  => Ticket::STATUS_TRANSFERRED,
        ]);
        return $ticket->fresh();
    }

    public function revoke(Ticket $ticket, ?string $reason = null): Ticket
    {
        if ($ticket->status === Ticket::STATUS_CANCELLED) return $ticket;
        $meta = $ticket->meta ?? [];
        if ($reason) $meta['revoke_reason'] = $reason;
        $ticket->update([
            'status'    => Ticket::STATUS_CANCELLED,
            'revoked_at'=> now(),
            'meta'      => $meta,
        ]);
        return $ticket->fresh();
    }

    public function redeemByHash(string $qrHash, ?int $scannerId = null, ?string $device=null, ?string $loc=null): Ticket
    {
        return DB::transaction(function () use ($qrHash, $scannerId, $device, $loc) {
            $ticket = Ticket::lockForUpdate()->where('qr_hash',$qrHash)->first();
            if (!$ticket) throw new DomainException('Ticket not found.');
            if ($ticket->status === Ticket::STATUS_CANCELLED) throw new DomainException('Ticket revoked.');
            if ($ticket->isRedeemed()) return $ticket;

            $ticket->update(['status'=>Ticket::STATUS_REDEEMED,'redeemed_at'=>now()]);

            TicketCheckin::create([
                'ticket_id' => $ticket->id,
                'scanned_by_user_id' => $scannerId,
                'scanned_at' => Carbon::now(),
                'device' => $device,
                'location' => $loc,
            ]);

            return $ticket->fresh();
        });
    }
}
