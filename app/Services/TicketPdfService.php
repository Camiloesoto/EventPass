<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TicketPdfService
{
    public function __construct(private QrCodeGenerator $qrCode)
    {
    }

    public function makePdf(Ticket $ticket): string
    {
        $ticket->loadMissing(['user', 'orderItem.ticketType']);

        $pageWidth = 612.0;
        $pageHeight = 396.0;
        $headerHeight = 90.0;

        $owner = optional($ticket->user)->name ?? 'Unassigned';
        $issuedAt = $ticket->created_at instanceof Carbon
            ? $ticket->created_at->format('M d, Y H:i')
            : now()->format('M d, Y H:i');

        $details = [
            'Holder: ' . $owner,
            'Ticket code: ' . $ticket->code,
            'Seat: ' . ($ticket->orderItem?->ticketType?->name ?? 'General admission'),
            'Status: ' . Str::title($ticket->status instanceof \UnitEnum ? $ticket->status->value : $ticket->status),
            'Issued at: ' . $issuedAt,
        ];

        $content = [];

        $content[] = sprintf('%.3f %.3f %.3f rg', 25 / 255, 40 / 255, 66 / 255);
        $content[] = sprintf('0 %.2f %.2f %.2f re f', $pageHeight - $headerHeight, $pageWidth, $headerHeight);

        $this->addText($content, 40, $pageHeight - 54, 24, 'EventPass Digital Ticket', [1, 1, 1]);

        $content[] = '1 w';
        $content[] = sprintf('%.3f %.3f %.3f RG', 239 / 255, 68 / 255, 68 / 255);
        $content[] = sprintf('20 40 %.2f %.2f re S', $pageWidth - 40, $pageHeight - 80);

        $bodyStartY = $pageHeight - $headerHeight - 36;
        $lineHeight = 18;
        foreach ($details as $index => $line) {
            $y = $bodyStartY - $index * $lineHeight;
            $this->addText($content, 40, $y, 12, $line, [0, 0, 0]);
        }

        $matrix = $this->addQuietZone($this->qrCode->matrix($ticket->qr_code_hash));
        $modules = count($matrix);
        $moduleSize = 6.0;
        $qrSize = $modules * $moduleSize;
        $qrLeft = $pageWidth - $qrSize - 70;
        $qrBottom = 100.0;

        $content[] = '0 0 0 rg';
        for ($row = 0; $row < $modules; $row++) {
            for ($col = 0; $col < $modules; $col++) {
                if ($matrix[$row][$col] !== 1) {
                    continue;
                }

                $x = $qrLeft + $col * $moduleSize;
                $y = $qrBottom + ($modules - 1 - $row) * $moduleSize;
                $content[] = sprintf('%.2f %.2f %.2f %.2f re f', $x, $y, $moduleSize, $moduleSize);
            }
        }

        $this->addText($content, $qrLeft, $qrBottom - 24, 10, 'Scan for entry verification', [0, 0, 0]);

        $contentStream = "q\n" . implode("\n", $content) . "\nQ";

        return $this->buildPdf($contentStream, $pageWidth, $pageHeight);
    }

    private function addText(array &$content, float $x, float $y, int $fontSize, string $text, array $rgb): void
    {
        $content[] = 'BT';
        $content[] = sprintf('%.3f %.3f %.3f rg', $rgb[0], $rgb[1], $rgb[2]);
        $content[] = sprintf('/F1 %d Tf', $fontSize);
        $content[] = sprintf('1 0 0 1 %.2f %.2f Tm', $x, $y);
        $content[] = '(' . $this->escapeText($text) . ') Tj';
        $content[] = 'ET';
    }

    private function addQuietZone(array $matrix, int $padding = 4): array
    {
        $size = count($matrix);
        $newSize = $size + $padding * 2;
        $result = array_fill(0, $newSize, array_fill(0, $newSize, 0));

        for ($row = 0; $row < $size; $row++) {
            for ($col = 0; $col < $size; $col++) {
                $result[$row + $padding][$col + $padding] = $matrix[$row][$col];
            }
        }

        return $result;
    }

    private function escapeText(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function buildPdf(string $content, float $pageWidth, float $pageHeight): string
    {
        $pdf = "%PDF-1.4\n";
        $offsets = [];

        $addObject = function (string $object) use (&$pdf, &$offsets): void {
            $offsets[] = strlen($pdf);
            $pdf .= $object . "\n";
        };

        $addObject("1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj");
        $addObject("2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj");

        $pageObject = sprintf(
            "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2f %.2f] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj",
            $pageWidth,
            $pageHeight
        );
        $addObject($pageObject);

        $addObject("4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj");

        $contentObject = sprintf(
            "5 0 obj << /Length %d >> stream\n%s\nendstream endobj",
            strlen($content),
            $content
        );
        $addObject($contentObject);

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n";
        $pdf .= '0 ' . (count($offsets) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf('%010d 00000 n ', $offset) . "\n";
        }

        $pdf .= sprintf("trailer << /Size %d /Root 1 0 R >>\n", count($offsets) + 1);
        $pdf .= "startxref\n" . $xrefPosition . "\n";
        $pdf .= "%%EOF";

        return $pdf;
    }
}
