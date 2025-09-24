<?php

namespace App\Services;

class QrCodeGenerator
{
    private const VERSION = 6;
    private const MODULE_COUNT = self::VERSION * 4 + 17;

    public function matrix(string $data): array
    {
        return $this->matrixLib($data);
    }

    public function renderSvg(string $data, int $scale = 8): string
    {
        if (class_exists(\Endroid\QrCode\Builder\Builder::class)) {
            /** @psalm-suppress UndefinedClass */
            $builder = \Endroid\QrCode\Builder\Builder::create()
                ->writer(new \Endroid\QrCode\Writer\SvgWriter())
                ->data($data)
                ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
                ->errorCorrectionLevel(new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow())
                ->margin(4)
                ->roundBlockSizeMode(new \Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin())
                ->size(max(1, $scale) * (self::MODULE_COUNT + 8));

            /** @psalm-suppress UndefinedMethod */
            $result = $builder->build();
            /** @psalm-suppress UndefinedMethod */
            return $result->getString();
        }

        $matrix = $this->matrix($data);
        $size = count($matrix);
        $quietZone = 4;
        $totalModules = $size + $quietZone * 2;
        $dimension = $totalModules * $scale;

        $rects = [];
        $rects[] = sprintf('<rect x="0" y="0" width="%d" height="%d" fill="#ffffff"/>', $dimension, $dimension);

        for ($row = 0; $row < $size; $row++) {
            for ($col = 0; $col < $size; $col++) {
                if ($matrix[$row][$col] !== 1) {
                    continue;
                }

                $x = ($col + $quietZone) * $scale;
                $y = ($row + $quietZone) * $scale;
                $rects[] = sprintf('<rect x="%d" y="%d" width="%d" height="%d" fill="#000000"/>', $x, $y, $scale, $scale);
            }
        }

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %1$d %1$d" width="%1$d" height="%1$d" shape-rendering="crispEdges">%2$s</svg>',
            $dimension,
            implode('', $rects)
        );
    }

    private function matrixLib(string $data): array
    {
        if (!class_exists(\BaconQrCode\Encoder\Encoder::class)) {
            throw new \RuntimeException('QR library not available');
        }
        $qr = \BaconQrCode\Encoder\Encoder::encode(
            $data,
            \BaconQrCode\Common\ErrorCorrectionLevel::L()
        );
        $bitMatrix = $qr->getMatrix();
        $size = $bitMatrix->getWidth();
        $out = [];
        for ($y = 0; $y < $size; $y++) {
            $row = [];
            for ($x = 0; $x < $size; $x++) {
                $row[] = $bitMatrix->get($x, $y) ? 1 : 0;
            }
            $out[] = $row;
        }
        return $out;
    }
}






