<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\File;

use Endroid\QrCode\Builder\Builder;

class QRCodeController extends Controller
{
    public function index(Request $request)
    {
        // Get the text or URL to encode
        $text = $request->input('text', 'Hello World');

        // QR code options
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG, // directly generate PNG
            'eccLevel'   => QRCode::ECC_L,            // error correction
            'scale'      => 5,
        ]);

        $qrcode = new QRCode($options);

        // Generate PNG binary
        $pngData = $qrcode->render($text);

        // Save PNG to public folder
        $path = public_path('qrcode.png');
        File::put($path, $pngData);

        // Return PNG for download
        return response()->download($path, 'qrcode.png', [
            'Content-Type' => 'image/png'
        ]);
    }

    // public function generate(Request $request)
    // {
    //     $text = $request->query('text', 'Hello World');

    //     $options = new QROptions([
    //         'version' => 5,
    //         'outputType' => QRCode::OUTPUT_IMAGE_PNG,
    //         'eccLevel' => QRCode::ECC_H,
    //         'scale' => 5,
    //         'imageBase64' => true, // easier for inline image
    //     ]);

    //     $qrcode = new QRCode($options);
    //     $pngData = $qrcode->render($text);

    //     return response()->json([
    //         'url' => $pngData // Base64 PNG string: can use directly in <img src="">
    //     ]);
    // }

    public function generate(Request $request)
    {
        // Get the text from query string (?text=...)
        $text = $request->query('text', 'Hello World');

        $options = new QROptions([
            'version'    => 5,
            'outputType' => QRCode::OUTPUT_IMAGE_PNG, // generate PNG
            'eccLevel'   => QRCode::ECC_L,
            'scale'      => 5,
        ]);

        $qrcode = new QRCode($options);
        $pngData = $qrcode->render($text);

        // Remove Base64 prefix
        $pngData = preg_replace('#^data:image/png;base64,#', '', $pngData);
        $binary = base64_decode($pngData);

        // Save to public folder
        $folder = public_path('qrcodes');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
        $filePath = $folder.'/qrcode.png';
        file_put_contents($filePath, $binary);

        // Return JSON with URL
        return response()->json([
            'url' => asset('qrcodes/qrcode.png')
        ]);
    }

}
