import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { QrCode } from 'lucide-react';
import { type FormEvent, useState } from 'react';

interface TicketDashboardProps {
    stats: {
        total: number;
        active: number;
        redeemed: number;
        cancelled: number;
    };
    statusBreakdown: Array<{
        status: string;
        count: number;
        percentage: number;
    }>;
    tickets: Array<{
        id: number;
        code: string;
        status: string;
        owner: { id: number; name: string; email: string } | null;
        issued_at: string | null;
        redeemed_at: string | null;
        qr_code_hash: string;
        pdf_url: string;
    }>;
    users: Array<{
        id: number;
        name: string;
        email: string;
    }>;
    statusOptions: string[];
    recentTickets: Array<{
        id: number;
        code: string;
        status: string;
        owner_name: string | null;
        issued_at: string | null;
        redeemed_at: string | null;
    }>;
    recentCheckins: Array<{
        id: number;
        ticket_code: string | null;
        scanner_name: string | null;
        scanned_at: string | null;
        location: string | null;
        device: string | null;
    }>;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Ticket administration',
        href: '/admin/tickets',
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
    if (!value) return '—';

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

const formatPercentage = (value: number) =>
    `${value.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 1 })}%`;

export default function TicketAdminDashboard({
    stats,
    statusBreakdown,
    tickets,
    users,
    statusOptions,
    recentTickets,
    recentCheckins,
}: TicketDashboardProps) {
    const defaultStatus = statusOptions.find((option) => option === 'issued') ?? statusOptions[0] ?? 'issued';

    type TicketItem = (typeof tickets)[number];

    type CreateTicketFormState = {
        order_item_id: string;
        user_id: string;
        status: string;
    };

    type EditTicketFormState = {
        user_id: string;
        status: string;
    };

    const createForm = useForm<CreateTicketFormState>({
        order_item_id: '',
        user_id: '',
        status: defaultStatus,
    });
    const editForm = useForm<EditTicketFormState>({
        user_id: '',
        status: defaultStatus,
    });

    const [isCreateOpen, setIsCreateOpen] = useState(false);
    const [isEditOpen, setIsEditOpen] = useState(false);
    const [editingTicket, setEditingTicket] = useState<TicketItem | null>(null);

    const handleCreateDialogChange = (open: boolean) => {
        setIsCreateOpen(open);
        if (!open) {
            createForm.reset();
            createForm.clearErrors();
        }
    };

    const handleEditDialogChange = (open: boolean) => {
        setIsEditOpen(open);
        if (!open) {
            setEditingTicket(null);
            editForm.reset();
            editForm.clearErrors();
        }
    };

    const handleCreateSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        const toInt = (value: string) => (value.trim().length > 0 ? Number.parseInt(value, 10) : null);

        const payload: Record<string, unknown> = {
            order_item_id: toInt(createForm.data.order_item_id),
            user_id: toInt(createForm.data.user_id),
            status: createForm.data.status,
        };

        createForm.transform(() => payload);
        createForm.post('/admin/tickets', {
            preserveScroll: true,
            onSuccess: () => handleCreateDialogChange(false),
            onFinish: () => createForm.transform((data) => data),
        });
    };

    const openEditDialog = (ticket: TicketItem) => {
        setEditingTicket(ticket);
        setIsEditOpen(true);
        editForm.setData('user_id', ticket.owner ? String(ticket.owner.id) : '');
        editForm.setData('status', ticket.status);
        editForm.clearErrors();
    };

    const handleEditSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        if (!editingTicket) {
            return;
        }

        const toInt = (value: string) => (value.trim().length > 0 ? Number.parseInt(value, 10) : null);

        const payload: Record<string, unknown> = {
            user_id: toInt(editForm.data.user_id),
            status: editForm.data.status,
        };

        editForm.transform(() => payload);
        editForm.put(`/admin/tickets/${editingTicket.id}`, {
            preserveScroll: true,
            onSuccess: () => handleEditDialogChange(false),
            onFinish: () => editForm.transform((data) => data),
        });
    };

    const handleDelete = (ticket: TicketItem) => {
        if (!window.confirm(`Delete ticket ${ticket.code}? This action cannot be undone.`)) {
            return;
        }

        router.delete(`/admin/tickets/${ticket.id}`, {
            preserveScroll: true,
            onFinish: () => {
                if (editingTicket?.id === ticket.id) {
                    handleEditDialogChange(false);
                }
            },
        });
    };

    const summaryCards = [
        {
            title: 'Total tickets',
            value: stats.total,
            description: 'Tickets issued overall',
        },
        {
            title: 'Active tickets',
            value: stats.active,
            description: 'Tickets that are still valid',
        },
        {
            title: 'Redeemed tickets',
            value: stats.redeemed,
            description: 'Tickets scanned for entry',
        },
        {
            title: 'Cancelled tickets',
            value: stats.cancelled,
            description: 'Tickets revoked or voided',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Ticket administration" />
            <div className="flex flex-1 flex-col gap-6 p-4">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <Heading
                        title="Ticket administration"
                        description="Monitor ticket volume, redemptions, and the most recent activity across your events."
                    />
                    <Button
                        type="button"
                        variant="secondary"
                        onClick={() => router.visit('/admin/tickets/scan')}
                    >
                        <QrCode className="mr-2 h-4 w-4" />
                        Launch scanner
                    </Button>
                </div>

                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    {summaryCards.map((card) => (
                        <Card key={card.title}>
                            <CardHeader>
                                <CardTitle className="text-sm font-medium text-muted-foreground">{card.title}</CardTitle>
                                <CardDescription>{card.description}</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <p className="text-3xl font-semibold tracking-tight">
                                    {card.value.toLocaleString()}
                                </p>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                <div className="grid gap-4 lg:grid-cols-[2fr,1fr]">
                    <Card className="overflow-hidden">
                        <CardHeader>
                            <CardTitle>Recent tickets</CardTitle>
                            <CardDescription>Latest tickets that have been issued or updated.</CardDescription>
                        </CardHeader>
                        <CardContent className="overflow-x-auto">
                            {recentTickets.length === 0 ? (
                                <p className="text-sm text-muted-foreground">No tickets have been issued yet.</p>
                            ) : (
                                <table className="min-w-full divide-y divide-border text-sm">
                                    <thead>
                                        <tr className="text-left text-xs font-medium uppercase tracking-wide text-muted-foreground">
                                            <th className="py-2 pr-4">Code</th>
                                            <th className="py-2 pr-4">Owner</th>
                                            <th className="py-2 pr-4">Status</th>
                                            <th className="py-2 pr-4">Issued</th>
                                            <th className="py-2">Redeemed</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border">
                                        {recentTickets.map((ticket) => (
                                            <tr key={ticket.id} className="align-top">
                                                <td className="py-3 pr-4 font-medium">{ticket.code}</td>
                                                <td className="py-3 pr-4 text-muted-foreground">
                                                    {ticket.owner_name ?? 'Unassigned'}
                                                </td>
                                                <td className="py-3 pr-4">
                                                    <Badge variant={statusVariants[ticket.status] ?? 'secondary'}>
                                                        {statusLabels[ticket.status] ?? ticket.status}
                                                    </Badge>
                                                </td>
                                                <td className="py-3 pr-4 text-muted-foreground">
                                                    {formatDateTime(ticket.issued_at)}
                                                </td>
                                                <td className="py-3 text-muted-foreground">
                                                    {formatDateTime(ticket.redeemed_at)}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Status breakdown</CardTitle>
                            <CardDescription>A quick glance at how tickets are distributed by status.</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {statusBreakdown.map((item) => (
                                <div key={item.status} className="space-y-2">
                                    <div className="flex items-center justify-between text-sm font-medium">
                                        <span>{statusLabels[item.status] ?? item.status}</span>
                                        <span>{item.count.toLocaleString()}</span>
                                    </div>
                                    <div className="h-2 w-full rounded-full bg-muted">
                                        <div
                                            className="h-full rounded-full bg-primary transition-all"
                                            style={{ width: `${item.percentage}%` }}
                                        />
                                    </div>
                                    <p className="text-xs text-muted-foreground">
                                        {formatPercentage(item.percentage)} of total tickets
                                    </p>
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                </div>

                <Card className="overflow-hidden">
                    <CardHeader className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <CardTitle>Manage tickets</CardTitle>
                            <CardDescription>
                                Issue new tickets or update existing ones directly from the dashboard.
                            </CardDescription>
                        </div>

                        <Dialog open={isCreateOpen} onOpenChange={handleCreateDialogChange}>
                            <DialogTrigger asChild>
                                <Button size="sm">Issue ticket</Button>
                            </DialogTrigger>

                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>Issue a ticket</DialogTitle>
                                    <DialogDescription>
                                        Link the ticket to an order item and assign an owner if needed.
                                    </DialogDescription>
                                </DialogHeader>

                                <form className="space-y-4" onSubmit={handleCreateSubmit}>
                                    <div className="grid gap-2">
                                        <Label htmlFor="create-order-item">Order item ID</Label>
                                        <Input
                                            id="create-order-item"
                                            type="number"
                                            min="1"
                                            placeholder="123"
                                            value={createForm.data.order_item_id}
                                            onChange={(event) => {
                                                createForm.setData('order_item_id', event.target.value);
                                                createForm.clearErrors('order_item_id');
                                            }}
                                            required
                                        />
                                        <InputError className="mt-1" message={createForm.errors.order_item_id} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="create-user">Owner user ID</Label>
                                        <Input
                                            id="create-user"
                                            type="number"
                                            min="1"
                                            placeholder="Leave blank for unassigned"
                                            value={createForm.data.user_id}
                                            onChange={(event) => {
                                                createForm.setData('user_id', event.target.value);
                                                createForm.clearErrors('user_id');
                                            }}
                                            list="ticket-user-options-create"
                                        />
                                        <datalist id="ticket-user-options-create">
                                            {users.map((user) => (
                                                <option key={user.id} value={user.id}>
                                                    {`${user.name} <${user.email}>`}
                                                </option>
                                            ))}
                                        </datalist>
                                        <p className="text-xs text-muted-foreground">Optional. Use a user ID from the list below.</p>
                                        <InputError className="mt-1" message={createForm.errors.user_id} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="create-status">Status</Label>
                                        <Select
                                            value={createForm.data.status}
                                            onValueChange={(value) => {
                                                createForm.setData('status', value);
                                                createForm.clearErrors('status');
                                            }}
                                        >
                                            <SelectTrigger id="create-status">
                                                <SelectValue placeholder="Select status" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {statusOptions.map((status) => (
                                                    <SelectItem key={status} value={status}>
                                                        {statusLabels[status] ?? status}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        <InputError className="mt-1" message={createForm.errors.status} />
                                    </div>

                                    <DialogFooter>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={() => handleCreateDialogChange(false)}
                                        >
                                            Cancel
                                        </Button>
                                        <Button type="submit" disabled={createForm.processing}>
                                            Issue ticket
                                        </Button>
                                    </DialogFooter>
                                </form>
                            </DialogContent>
                        </Dialog>
                    </CardHeader>

                    <CardContent className="overflow-x-auto">
                        {tickets.length === 0 ? (
                            <p className="text-sm text-muted-foreground">
                                No tickets available yet. Use the button above to issue the first one.
                            </p>
                        ) : (
                            <table className="min-w-full divide-y divide-border text-sm">
                                <thead>
                                    <tr className="text-left text-xs font-medium uppercase tracking-wide text-muted-foreground">
                                        <th className="py-2 pr-4">Code</th>
                                        <th className="py-2 pr-4">Owner</th>
                                        <th className="py-2 pr-4">Status</th>
                                        <th className="py-2 pr-4">Issued</th>
                                        <th className="py-2 pr-4">Redeemed</th>
                                        <th className="py-2 pr-4">QR hash</th>
                                        <th className="py-2 pr-4">PDF file</th>
                                        <th className="py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border">
                                    {tickets.map((ticket) => (
                                        <tr key={ticket.id} className="align-top">
                                            <td className="py-3 pr-4 font-medium">{ticket.code}</td>
                                            <td className="py-3 pr-4">
                                                {ticket.owner ? (
                                                    <div className="flex flex-col">
                                                        <span className="font-medium text-foreground">{ticket.owner.name}</span>
                                                        <span className="text-xs text-muted-foreground">{ticket.owner.email}</span>
                                                    </div>
                                                ) : (
                                                    <span className="text-muted-foreground">Unassigned</span>
                                                )}
                                            </td>
                                            <td className="py-3 pr-4">
                                                <Badge variant={statusVariants[ticket.status] ?? 'secondary'}>
                                                    {statusLabels[ticket.status] ?? ticket.status}
                                                </Badge>
                                            </td>
                                            <td className="py-3 pr-4 text-muted-foreground">
                                                {formatDateTime(ticket.issued_at)}
                                            </td>
                                            <td className="py-3 pr-4 text-muted-foreground">
                                                {formatDateTime(ticket.redeemed_at)}
                                            </td>
                                            <td className="py-3 pr-4">
                                                <code className="text-[11px] text-muted-foreground break-all">
                                                    {ticket.qr_code_hash}
                                                </code>
                                            </td>
                                            <td className="py-3 pr-4 text-xs text-muted-foreground break-all">
                                                {ticket.pdf_url || '—'}
                                            </td>
                                            <td className="py-3">
                                                <div className="flex flex-wrap gap-2">
                                                    <Button
                                                        type="button"
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => openEditDialog(ticket)}
                                                    >
                                                        Edit
                                                    </Button>
                                                    <Button
                                                        type="button"
                                                        variant="destructive"
                                                        size="sm"
                                                        onClick={() => handleDelete(ticket)}
                                                    >
                                                        Delete
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        )}
                    </CardContent>
                </Card>

                <Dialog open={isEditOpen} onOpenChange={handleEditDialogChange}>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Edit ticket</DialogTitle>
                            <DialogDescription>
                                Update ticket {editingTicket?.code ?? ''} ownership and status.
                            </DialogDescription>
                        </DialogHeader>

                        <form className="space-y-4" onSubmit={handleEditSubmit}>
                            <div className="grid gap-2">
                                <Label htmlFor="edit-user">Owner user ID</Label>
                                <Input
                                    id="edit-user"
                                    type="number"
                                    min="1"
                                    placeholder="Leave blank for unassigned"
                                    value={editForm.data.user_id}
                                    onChange={(event) => {
                                        editForm.setData('user_id', event.target.value);
                                        editForm.clearErrors('user_id');
                                    }}
                                    list="ticket-user-options-edit"
                                />
                                <datalist id="ticket-user-options-edit">
                                    {users.map((user) => (
                                        <option key={user.id} value={user.id}>
                                            {`${user.name} <${user.email}>`}
                                        </option>
                                    ))}
                                </datalist>
                                <p className="text-xs text-muted-foreground">
                                    Optional. Choose from the suggestions or clear the value to remove the owner.
                                </p>
                                <InputError className="mt-1" message={editForm.errors.user_id} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="edit-status">Status</Label>
                                <Select
                                    value={editForm.data.status}
                                    onValueChange={(value) => {
                                        editForm.setData('status', value);
                                        editForm.clearErrors('status');
                                    }}
                                >
                                    <SelectTrigger id="edit-status">
                                        <SelectValue placeholder="Select status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {statusOptions.map((status) => (
                                            <SelectItem key={status} value={status}>
                                                {statusLabels[status] ?? status}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError className="mt-1" message={editForm.errors.status} />
                            </div>

                            <DialogFooter>
                                <Button type="button" variant="outline" onClick={() => handleEditDialogChange(false)}>
                                    Cancel
                                </Button>
                                <Button type="submit" disabled={editForm.processing}>
                                    Save changes
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                <Card>
                    <CardHeader>
                        <CardTitle>Recent check-ins</CardTitle>
                        <CardDescription>Track the latest scans made by staff at your entry points.</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        {recentCheckins.length === 0 ? (
                            <p className="text-sm text-muted-foreground">No check-ins recorded yet.</p>
                        ) : (
                            recentCheckins.map((checkin) => (
                                <div key={checkin.id} className="rounded-lg border p-4">
                                    <div className="flex items-center justify-between text-sm font-medium">
                                        <span>{checkin.ticket_code ?? 'Unknown ticket'}</span>
                                        <span className="text-xs text-muted-foreground">
                                            {formatDateTime(checkin.scanned_at)}
                                        </span>
                                    </div>
                                    <div className="mt-2 grid gap-2 text-sm text-muted-foreground sm:grid-cols-2">
                                        <div>
                                            <span className="font-medium text-foreground">Scanner:</span>{' '}
                                            {checkin.scanner_name ?? '—'}
                                        </div>
                                        <div>
                                            <span className="font-medium text-foreground">Device:</span>{' '}
                                            {checkin.device ?? '—'}
                                        </div>
                                        <div className="sm:col-span-2">
                                            <span className="font-medium text-foreground">Location:</span>{' '}
                                            {checkin.location ?? '—'}
                                        </div>
                                    </div>
                                </div>
                            ))
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
