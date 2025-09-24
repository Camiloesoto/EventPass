import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import type { FormEvent } from 'react';
import { Head } from '@inertiajs/react';

import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

interface TicketScanPageProps {
    redeemRoute: string;
}

interface RedeemedTicket {
    id?: number;
    code?: string;
    status?: string;
    redeemed_at?: string | null;
    user_id?: number | null;
    meta?: Record<string, unknown> | null;
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
    {
        title: 'Scan & redeem',
        href: '/admin/tickets/scan',
    },
];

const formatDateTime = (value?: string | null) => {
    if (!value) {
        return '--';
    }

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

const statusLabel = (status?: string) => {
    if (!status) return 'Unknown';
    return status.charAt(0).toUpperCase() + status.slice(1);
};

const hexHashRegex = /^[0-9a-f]{64}$/i;
const ticketCodeRegex = /^[A-Z0-9]{10,32}$/;

type ScannerSupport = 'checking' | 'native' | 'zxing' | 'none';

type ZXingControls = { stop: () => void };

type ZXingReader = {
    decodeFromVideoDevice: (
        deviceId: string | undefined,
        video: HTMLVideoElement,
        callback: (
            result: { getText(): string } | undefined,
            error: unknown,
            controls: ZXingControls
        ) => void
    ) => Promise<ZXingControls>;
    reset: () => void;
};

const isValidIdentifier = (value: string) => {
    const trimmed = value.trim();
    if (!trimmed) {
        return false;
    }

    if (hexHashRegex.test(trimmed)) {
        return true;
    }

    return ticketCodeRegex.test(trimmed.toUpperCase());
};

const normaliseIdentifier = (value: string) => {
    const trimmed = value.trim();
    if (hexHashRegex.test(trimmed)) {
        return trimmed.toLowerCase();
    }

    return trimmed.toUpperCase();
};

export default function TicketAdminScan({ redeemRoute }: TicketScanPageProps) {
    const videoRef = useRef<HTMLVideoElement | null>(null);
    const streamRef = useRef<MediaStream | null>(null);
    const frameRef = useRef<number | null>(null);
    const detectorRef = useRef<BarcodeDetector | null>(null);
    const zxingReaderRef = useRef<ZXingReader | null>(null);
    const zxingControlsRef = useRef<ZXingControls | null>(null);

    const [scannerSupport, setScannerSupport] = useState<ScannerSupport>('checking');
    const [scanning, setScanning] = useState(false);
    const [manualToken, setManualToken] = useState('');
    const [locationLabel, setLocationLabel] = useState('');
    const [deviceLabel, setDeviceLabel] = useState(() => (typeof navigator !== 'undefined' ? navigator.userAgent : ''));
    const [lastTicket, setLastTicket] = useState<RedeemedTicket | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);
    const [cameraError, setCameraError] = useState<string | null>(null);
    const [redeeming, setRedeeming] = useState(false);
    const [lastScanValue, setLastScanValue] = useState<string | null>(null);

    useEffect(() => {
        let active = true;

        const checkSupport = async () => {
            if (typeof window === 'undefined') {
                return;
            }

            if ('BarcodeDetector' in window) {
                if (active) setScannerSupport('native');
                return;
            }

            try {
                await import('@zxing/browser');
                if (active) setScannerSupport('zxing');
            } catch {
                if (active) setScannerSupport('none');
            }
        };

        void checkSupport();

        return () => {
            active = false;
        };
    }, []);

    const stopStream = useCallback(() => {
        if (frameRef.current !== null) {
            cancelAnimationFrame(frameRef.current);
            frameRef.current = null;
        }

        if (videoRef.current) {
            videoRef.current.pause();
            videoRef.current.srcObject = null;
        }

        if (streamRef.current) {
            streamRef.current.getTracks().forEach((track) => track.stop());
            streamRef.current = null;
        }

        if (zxingControlsRef.current) {
            try {
                zxingControlsRef.current.stop();
            } catch (error) {
                console.error('Failed to stop ZXing controls', error);
            }
            zxingControlsRef.current = null;
        }

        if (zxingReaderRef.current) {
            try {
                zxingReaderRef.current.reset();
            } catch (error) {
                console.error('Failed to reset ZXing reader', error);
            }
        }

        detectorRef.current = null;
    }, []);

    useEffect(() => stopStream, [stopStream]);

    const redeemTicket = useCallback(
        async (identifier: string, fromScan = false) => {
            if (!identifier || redeeming) {
                return;
            }

            const payload = identifier.trim();
            if (!isValidIdentifier(payload)) {
                setErrorMessage('The scanned value is not a valid ticket code or hash.');
                return;
            }

            const normalized = normaliseIdentifier(payload);

            setRedeeming(true);
            setErrorMessage(null);

            try {
                const headers: Record<string, string> = {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                };
                const csrf = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
                if (csrf?.content) {
                    headers['X-CSRF-TOKEN'] = csrf.content;
                }
                const device = deviceLabel.trim();
                const location = locationLabel.trim();
                if (device) headers['X-Device'] = device;
                if (location) headers['X-Location'] = location;

                const response = await fetch(redeemRoute, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({ qr_code_hash: normalized }),
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    let message = 'Unable to redeem the ticket.';
                    try {
                        const data = await response.json();
                        if (typeof data?.message === 'string') {
                            message = data.message;
                        } else if (data?.errors?.qr_code_hash?.length) {
                            message = data.errors.qr_code_hash[0];
                        }
                    } catch (error) {
                        console.error('Failed to parse redemption error', error);
                    }
                    throw new Error(message);
                }

                const ticket = (await response.json()) as RedeemedTicket;
                setLastTicket(ticket);
                setErrorMessage(null);
                setManualToken('');
                setLastScanValue(normalized);

                if (fromScan) {
                    setCameraError(null);
                }
            } catch (error) {
                const message = error instanceof Error ? error.message : 'Unable to redeem the ticket.';
                setErrorMessage(message);
            } finally {
                setRedeeming(false);
            }
        },
        [deviceLabel, locationLabel, redeemRoute, redeeming]
    );

    useEffect(() => {
        if (!scanning) {
            stopStream();
            return;
        }

        if (scannerSupport === 'checking') {
            return;
        }

        if (scannerSupport === 'none') {
            setCameraError('This browser does not support QR code detection.');
            setScanning(false);
            return;
        }

        let cancelled = false;

        const startNativeScanner = async () => {
            try {
                const fmts = (await window.BarcodeDetector.getSupportedFormats?.()) ?? ['qr_code'];
                const wanted = fmts.includes('qr_code') ? ['qr_code'] : fmts;
                detectorRef.current = new window.BarcodeDetector({ formats: wanted as unknown as BarcodeFormat[] });
            } catch (error) {
                console.error('Failed to initialise barcode detector', error);
                setCameraError('This browser does not support QR code detection.');
                setScanning(false);
                return;
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } },
                    audio: false,
                });

                if (cancelled) {
                    stream.getTracks().forEach((track) => track.stop());
                    return;
                }

                streamRef.current = stream;
                const video = videoRef.current;
                if (video) {
                    video.srcObject = stream;
                    video.playsInline = true;
                    video.muted = true;
                    await video.play();
                }

                const scanFrame = async () => {
                    if (cancelled || !detectorRef.current || !videoRef.current) {
                        return;
                    }

                    try {
                        const barcodes = await detectorRef.current.detect(videoRef.current);
                        const match = barcodes.find((barcode) => barcode.rawValue);
                        if (match?.rawValue) {
                            const value = match.rawValue.trim();
                            if (!value) {
                                frameRef.current = requestAnimationFrame(scanFrame);
                                return;
                            }

                            const normalized = normaliseIdentifier(value);
                            if (normalized !== lastScanValue) {
                                setScanning(false);
                                await redeemTicket(value, true);
                                return;
                            }
                        }
                    } catch (error) {
                        console.error('QR detection failed', error);
                    }

                    frameRef.current = requestAnimationFrame(scanFrame);
                };

                frameRef.current = requestAnimationFrame(scanFrame);
            } catch (error) {
                console.error('Unable to access camera', error);
                setCameraError(error instanceof Error ? error.message : 'Unable to access camera.');
                setScanning(false);
            }
        };

        const startZxingScanner = async () => {
            try {
                const { BrowserMultiFormatReader } = await import('@zxing/browser');
                const reader = zxingReaderRef.current ?? new BrowserMultiFormatReader();
                zxingReaderRef.current = reader;

                const video = videoRef.current;
                if (!video) {
                    throw new Error('Video element is not available.');
                }

                const controls = await reader.decodeFromVideoDevice(undefined, video, async (result, error, controls) => {
                    if (cancelled) {
                        controls.stop();
                        return;
                    }

                    if (!result) {
                        return;
                    }

                    const value = result.getText().trim();
                    if (!value) {
                        return;
                    }

                    const normalized = normaliseIdentifier(value);
                    if (normalized === lastScanValue) {
                        return;
                    }

                    zxingControlsRef.current = controls;
                    setScanning(false);
                    await redeemTicket(value, true);
                });

                zxingControlsRef.current = controls;
            } catch (error) {
                console.error('ZXing scanner failed', error);
                setCameraError(error instanceof Error ? error.message : 'Unable to access camera.');
                setScanning(false);
            }
        };

        const startScanner = async () => {
            if (scannerSupport === 'native') {
                await startNativeScanner();
            } else {
                await startZxingScanner();
            }
        };

        void startScanner();

        return () => {
            cancelled = true;
            stopStream();
        };
    }, [lastScanValue, redeemTicket, scannerSupport, scanning, stopStream]);

    const manualTokenValid = useMemo(() => isValidIdentifier(manualToken), [manualToken]);

    const handleManualSubmit = useCallback(
        (event: FormEvent<HTMLFormElement>) => {
            event.preventDefault();
            setScanning(false);
            void redeemTicket(manualToken.trim(), false);
        },
        [manualToken, redeemTicket]
    );

    const handleStartScanning = useCallback(() => {
        if (scannerSupport === 'none' || scannerSupport === 'checking') {
            return;
        }

        setErrorMessage(null);
        setCameraError(null);
        setLastScanValue(null);
        setScanning(true);
    }, [scannerSupport]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Scan & redeem tickets" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-y-auto rounded-xl p-4">
                <Card className="h-full">
                    <CardHeader>
                        <CardTitle>Scan tickets for entry</CardTitle>
                        <CardDescription>
                            Use the built-in scanner or enter a ticket code / QR hash manually to redeem attendee tickets on arrival.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        {scannerSupport === 'none' && (
                            <Alert variant="destructive">
                                <AlertTitle>Scanning not supported</AlertTitle>
                                <AlertDescription>
                                    This browser does not support the Barcode Detector API. You can still redeem tickets by
                                    entering the QR hash manually.
                                </AlertDescription>
                            </Alert>
                        )}

                        {cameraError && (
                            <Alert variant="destructive">
                                <AlertTitle>Camera error</AlertTitle>
                                <AlertDescription>{cameraError}</AlertDescription>
                            </Alert>
                        )}

                        {errorMessage && (
                            <Alert variant="destructive">
                                <AlertTitle>Redeem failed</AlertTitle>
                                <AlertDescription>{errorMessage}</AlertDescription>
                            </Alert>
                        )}

                        <div className="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)]">
                            <div className="space-y-4">
                                <div className="relative overflow-hidden rounded-xl border bg-muted/40 shadow-inner">
                                    <div className="absolute left-1/2 top-1/2 z-10 -translate-x-1/2 -translate-y-1/2 pointer-events-none select-none rounded-lg border border-dashed border-white/70 px-6 py-12 text-center text-white/80">
                                        <p className="text-sm font-medium">Align the QR code within the frame</p>
                                        <p className="text-xs text-white/60">We&apos;ll capture it automatically</p>
                                    </div>
                                    <video
                                        ref={videoRef}
                                        className={`h-72 w-full bg-black object-cover ${scanning ? 'opacity-100' : 'opacity-60'}`}
                                        autoPlay
                                        muted
                                        playsInline
                                    />
                                </div>
                                <div className="flex flex-wrap items-center gap-3">
                                    <Button
                                        onClick={scanning ? () => setScanning(false) : handleStartScanning}
                                        disabled={scannerSupport === 'none' || scannerSupport === 'checking' || redeeming}
                                    >
                                        {scanning ? 'Stop scanning' : 'Start scanning'}
                                    </Button>
                                    <span className="text-sm text-muted-foreground">
                                        {scannerSupport === 'checking'
                                            ? 'Checking camera capabilities...'
                                            : scannerSupport === 'none'
                                                ? 'This browser does not support camera scanning.'
                                                : scanning
                                                    ? 'Scanning in progress...'
                                                    : 'Ready to scan the next ticket.'}
                                    </span>
                                </div>
                            </div>

                            <div className="space-y-4">
                                <form className="space-y-4" onSubmit={handleManualSubmit}>
                                    <div className="grid gap-2">
                                        <Label htmlFor="manual-token">Ticket code or QR hash</Label>
                                        <Input
                                            id="manual-token"
                                            name="qr_code_hash"
                                            value={manualToken}
                                            onChange={(event) => setManualToken(event.target.value)}
                                            placeholder="Enter the ticket code or paste the QR hash"
                                            maxLength={64}
                                            autoComplete="off"
                                        />
                                        <p className="text-xs text-muted-foreground">
                                            Scan the printed ticket code (e.g. ABC123) or paste the 64-character hash located beneath the QR image.
                                        </p>
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="device-label">Scanner device label</Label>
                                        <Input
                                            id="device-label"
                                            value={deviceLabel}
                                            onChange={(event) => setDeviceLabel(event.target.value)}
                                            placeholder="Front gate iPad"
                                        />
                                        <p className="text-xs text-muted-foreground">
                                            Optional. Helps identify which device processed the check-in.
                                        </p>
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="location-label">Checkpoint location</Label>
                                        <Input
                                            id="location-label"
                                            value={locationLabel}
                                            onChange={(event) => setLocationLabel(event.target.value)}
                                            placeholder="Gate A"
                                        />
                                        <p className="text-xs text-muted-foreground">
                                            Optional. Stored with the check-in record for reporting.
                                        </p>
                                    </div>

                                    <div className="flex flex-wrap items-center gap-3">
                                        <Button type="submit" disabled={!manualTokenValid || redeeming}>
                                            {redeeming ? 'Redeeming...' : 'Redeem manually'}
                                        </Button>
                                        <Button type="button" variant="outline" onClick={() => setManualToken('')}>
                                            Clear
                                        </Button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {lastTicket && (
                            <div className="rounded-xl border border-primary/40 bg-primary/5 p-4">
                                <div className="flex flex-wrap items-center gap-3">
                                    <h3 className="text-base font-semibold text-primary-foreground/90">
                                        Ticket {lastTicket.code ?? `#${lastTicket.id ?? ''}`}
                                    </h3>
                                    <Badge>{statusLabel(lastTicket.status)}</Badge>
                                </div>
                                <dl className="mt-3 grid gap-4 text-sm sm:grid-cols-2">
                                    <div>
                                        <dt className="text-muted-foreground">Ticket ID</dt>
                                        <dd className="font-medium text-foreground">{lastTicket.id ?? 'Unknown'}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-muted-foreground">Redeemed at</dt>
                                        <dd className="font-medium text-foreground">{formatDateTime(lastTicket.redeemed_at ?? null)}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-muted-foreground">Owner user ID</dt>
                                        <dd className="font-medium text-foreground">{lastTicket.user_id ?? 'Unassigned'}</dd>
                                    </div>
                                </dl>
                                <div className="mt-4 flex flex-wrap items-center gap-3">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={handleStartScanning}
                                        disabled={scannerSupport === 'none' || scannerSupport === 'checking' || scanning}
                                    >
                                        Scan next ticket
                                    </Button>
                                    {lastScanValue && (
                                        <span className="text-xs text-muted-foreground break-all">
                                            Last identifier: {lastScanValue}
                                        </span>
                                    )}
                                </div>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}














