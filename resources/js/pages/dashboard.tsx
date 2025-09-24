import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

type Metrics = {
    events_upcoming: number;
    orders_total: number;
    orders_paid: number;
    tickets_total: number;
    tickets_active: number;
    tickets_redeemed: number;
    payments_total_amount: number;
    payments_last_7_days: number;
    my_tickets: number;
    my_orders: number;
};

interface DashboardProps {
    metrics: Metrics;
    isAdmin: boolean;
    links: {
        events: string;
        myTickets: string;
        adminTickets: string | null;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

export default function Dashboard({ metrics, isAdmin, links }: DashboardProps) {
    const currency = (value: number) =>
        value.toLocaleString(undefined, { style: 'currency', currency: 'USD', maximumFractionDigits: 2 });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-sm font-medium text-muted-foreground">Upcoming events</CardTitle>
                            <CardDescription>Published events scheduled in the future</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <p className="text-3xl font-semibold tracking-tight">{metrics.events_upcoming}</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-sm font-medium text-muted-foreground">Orders</CardTitle>
                            <CardDescription>Paid vs total orders</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <p className="text-3xl font-semibold tracking-tight">
                                {metrics.orders_paid.toLocaleString()} / {metrics.orders_total.toLocaleString()}
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-sm font-medium text-muted-foreground">Tickets</CardTitle>
                            <CardDescription>Active vs total tickets</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <p className="text-3xl font-semibold tracking-tight">
                                {metrics.tickets_active.toLocaleString()} / {metrics.tickets_total.toLocaleString()}
                            </p>
                            <p className="mt-1 text-xs text-muted-foreground">
                                Redeemed: {metrics.tickets_redeemed.toLocaleString()}
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-sm font-medium text-muted-foreground">Revenue</CardTitle>
                            <CardDescription>Total and last 7 days (paid)</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <p className="text-3xl font-semibold tracking-tight">{currency(metrics.payments_total_amount)}</p>
                            <p className="mt-1 text-xs text-muted-foreground">
                                Last 7 days: {currency(metrics.payments_last_7_days)}
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>My activity</CardTitle>
                            <CardDescription>Quick glance at your orders and tickets</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <div className="text-xs uppercase tracking-wide text-muted-foreground">My orders</div>
                                    <div className="text-2xl font-semibold">{metrics.my_orders.toLocaleString()}</div>
                                </div>
                                <div>
                                    <div className="text-xs uppercase tracking-wide text-muted-foreground">My tickets</div>
                                    <div className="text-2xl font-semibold">{metrics.my_tickets.toLocaleString()}</div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Quick links</CardTitle>
                            <CardDescription>Jump to common areas</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="flex flex-wrap gap-3">
                                <Button asChild variant="secondary">
                                    <a href={links.events}>Browse events</a>
                                </Button>
                                <Button asChild>
                                    <a href={links.myTickets}>My tickets</a>
                                </Button>
                                {isAdmin && links.adminTickets && (
                                    <Button asChild variant="outline">
                                        <a href={links.adminTickets}>Ticket administration</a>
                                    </Button>
                                )}
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
