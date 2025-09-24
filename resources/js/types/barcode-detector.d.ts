export {};

declare global {
    interface Window {
        BarcodeDetector?: typeof BarcodeDetector;
    }
}

interface BarcodeDetectorOptions {
    formats?: string[];
}

interface DetectedBarcode {
    rawValue: string;
    format: string;
    cornerPoints?: DOMPoint[];
}

declare class BarcodeDetector {
    constructor(options?: BarcodeDetectorOptions);
    detect(source: CanvasImageSource | ImageBitmap): Promise<DetectedBarcode[]>;
    static getSupportedFormats(): Promise<string[]>;
}
