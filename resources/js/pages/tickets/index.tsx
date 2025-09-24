import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { Download, QrCode } from 'lucide-react';
import { type FormEvent, useState } from 'react';

interface TicketCheckin {
    scanned_at: string | null;
    location: string | null;
    device: string | null;
}

interface TicketItem {
    id: number;
    code: string;
    status: string;
    issued_at: string | null;
    redeemed_at: string | null;
    qr_code_hash: string;
    pdf_url: string;
    latest_checkin: TicketCheckin | null;
    download_url: string;
    qr_url: string;
}

interface TicketsPageProps {
    tickets: TicketItem[];
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'My tickets',
        href: '/tickets',
    },
];

const statusLabels: Record<string, string> = {
    issued: 'Issued',
    transferred: 'Transferred',
    redeemed: 'Redeemed',
    cancelled: 'Cancelled',
};

const statusVariants: Record<string, 'default' | 'secondary' | 'destructive'> = {
    issued: 'secondary',
    transferred: 'secondary',
    redeemed: 'default',
    cancelled: 'destructive',
};

const formatDateTime = (value: string | null) => {
    if (!value) return '--';

    const isoLike = value.replace(' ', 'T');
    const date = new Date(isoLike);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleString(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
};

const isTransferDisabled = (ticket: TicketItem) => ['redeemed', 'cancelled'].includes(ticket.status);

type TransferFormState = {
    to_email: string;
};

export default function TicketsIndex({ tickets }: TicketsPageProps) {
    const [transferDialogOpen, setTransferDialogOpen] = useState(false);
    const [selectedTicket, setSelectedTicket] = useState<TicketItem | null>(null);
    const transferForm = useForm<TransferFormState>({
        to_email: '',
    });

    const openTransferDialog = (ticket: TicketItem) => {
        setSelectedTicket(ticket);
        transferForm.reset();
        transferForm.clearErrors();
        setTransferDialogOpen(true);
    };

    const handleTransferDialogChange = (open: boolean) => {
        setTransferDialogOpen(open);

        if (!open) {
            setSelectedTicket(null);
            transferForm.reset();
            transferForm.clearErrors();
        }
    };

    const handleTransferSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        if (!selectedTicket) {
            return;
        }

        transferForm.post(`/tickets/${selectedTicket.id}/transfer`, {
            preserveScroll: true,
            onSuccess: () => {
                handleTransferDialogChange(false);
                router.reload({ only: ['tickets'] });
            },
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="My tickets" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <Card className="h-full">
                    <CardHeader>
                        <CardTitle>My tickets</CardTitle>
                        <CardDescription>Access the tickets that have been issued to you.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {tickets.length === 0 ? (
                            <p className="text-sm text-muted-foreground">
                                You don&apos;t have any tickets yet. When tickets are assigned to you, they will appear here.
                            </p>
                        ) : (
                            <div className="flex flex-col gap-4">
                                {tickets.map((ticket) => {
                                    const variant = statusVariants[ticket.status] ?? 'secondary';
                                    const label = statusLabels[ticket.status] ?? ticket.status;
                                    const transferDisabled = isTransferDisabled(ticket);

                                    return (
                                        <div
                                            key={ticket.id}
                                            className="rounded-lg border border-sidebar-border/70 bg-sidebar p-4 shadow-sm dark:border-sidebar-border"
                                        >
                                            <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <span className="text-xs uppercase tracking-wide text-muted-foreground">
                                                        Ticket code
                                                    </span>
                                                    <div className="font-mono text-lg font-semibold text-foreground">
                                                        {ticket.code}
                                                    </div>
                                                </div>
                                                <Badge variant={variant} className="w-fit capitalize">
                                                    {label}
                                                </Badge>
                                            </div>

                                            <dl className="mt-4 grid gap-4 text-sm md:grid-cols-2">
                                                <div>
                                                    <dt className="text-muted-foreground">Issued</dt>
                                                    <dd className="font-medium text-foreground">{formatDateTime(ticket.issued_at)}</dd>
                                                </div>
                                                <div>
                                                    <dt className="text-muted-foreground">Redeemed</dt>
                                                    <dd className="font-medium text-foreground">{formatDateTime(ticket.redeemed_at)}</dd>
                                                </div>
                                                <div>
                                                    <dt className="text-muted-foreground">QR hash</dt>
                                                    <dd className="font-mono text-xs text-foreground break-all">{ticket.qr_code_hash}</dd>
                                                </div>
                                                <div>
                                                    <dt className="text-muted-foreground">PDF file</dt>
                                                    <dd className="font-mono text-xs text-foreground break-all">{ticket.pdf_url || '—'}</dd>
                                                </div>
                                                {ticket.latest_checkin && (
                                                    <div>
                                                        <dt className="text-muted-foreground">Last checked in</dt>
                                                        <dd className="space-y-1 font-medium text-foreground">
                                                            <div>{formatDateTime(ticket.latest_checkin.scanned_at)}</div>
                                                            {(ticket.latest_checkin.location || ticket.latest_checkin.device) && (
                                                                <div className="text-xs text-muted-foreground">
                                                                    {[ticket.latest_checkin.location, ticket.latest_checkin.device]
                                                                        .filter(Boolean)
                                                                        .join(' - ')}
                                                                </div>
                                                            )}
                                                        </dd>
                                                    </div>
                                                )}
                                            </dl>

                                            <div className="mt-4 flex flex-wrap items-center gap-2">
                                                <Button asChild variant="secondary">
                                                    <a href={ticket.download_url} download>
                                                        <Download className="mr-2 h-4 w-4" />
                                                        Download PDF
                                                    </a>
                                                </Button>
                                                <Dialog>
                                                    <DialogTrigger asChild>
                                                        <Button variant="outline">
                                                            <QrCode className="mr-2 h-4 w-4" />
                                                            View QR code
                                                        </Button>
                                                    </DialogTrigger>
                                                    <DialogContent className="max-w-sm">
                                                        <DialogHeader>
                                                            <DialogTitle>Ticket QR code</DialogTitle>
                                                            <DialogDescription>Present this code to validate entry for ticket {ticket.code}.</DialogDescription>
                                                        </DialogHeader>
                                                        <div className="flex flex-col items-center gap-4">
                                                            <div className="rounded-md border bg-background p-4">
                                                                <img
                                                                    src={ticket.qr_url}
                                                                    alt={`QR code for ticket ${ticket.code}`}
                                                                    className="h-60 w-60 object-contain"
                                                                />
                                                            </div>
                                                            <code className="text-xs text-muted-foreground">{ticket.code}</code>
                                                            <code className="break-all text-[10px] text-muted-foreground">{ticket.qr_code_hash}</code>
                                                        </div>
                                                    </DialogContent>
                                                </Dialog>
                                            </div>

                                            <div className="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                                <Button
                                                    variant="outline"
                                                    disabled={transferDisabled}
                                                    onClick={() => openTransferDialog(ticket)}
                                                >
                                                    Transfer ticket
                                                </Button>
                                                {transferDisabled && (
                                                    <p className="text-xs text-muted-foreground">
                                                        This ticket can&apos;t be transferred while it is {ticket.status}.
                                                    </p>
                                                )}
                                            </div>

                                        </div>
                                    );
                                })}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>

            <Dialog open={transferDialogOpen} onOpenChange={handleTransferDialogChange}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Transfer ticket</DialogTitle>
                        <DialogDescription>
                            {selectedTicket
                                ? `Send ticket ${selectedTicket.code} to another registered user by entering their email address.`
                                : 'Select a ticket from the list to start a transfer.'}
                        </DialogDescription>
                    </DialogHeader>
                    <form className="space-y-4" onSubmit={handleTransferSubmit}>
                        <div className="grid gap-2">
                            <Label htmlFor="transfer-email">Recipient email</Label>
                            <Input
                                id="transfer-email"
                                type="email"
                                name="to_email"
                                placeholder="recipient@example.com"
                                value={transferForm.data.to_email}
                                onChange={(event) => {
                                    transferForm.setData('to_email', event.target.value);
                                    transferForm.clearErrors('to_email', 'to_user_id');
                                }}
                                required
                                autoComplete="email"
                                autoFocus
                            />
                            <InputError message={transferForm.errors.to_email ?? transferForm.errors.to_user_id} />
                        </div>
                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => handleTransferDialogChange(false)}
                                disabled={transferForm.processing}
                            >
                                Cancel
                            </Button>
                            <Button type="submit" disabled={transferForm.processing || !selectedTicket}>
                                {transferForm.processing ? 'Transferring...' : 'Send transfer'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
