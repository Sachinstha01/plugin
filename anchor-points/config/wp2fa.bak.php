<?php
use PragmaRX\Google2FAQRCode\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

require_once(ABSPATH . "/vendor/autoload.php");

$google2fa = new Google2FA();
// $qsCode= new Google2FAQRCode();
$secret_key = $google2fa->generateSecretKey(32);

//     $qrCodeUrl = $google2fa->getQRCodeUrl($companyName,$companyEmail,$secretKey);

$g2faUrl = $google2fa->getQRCodeUrl(
    'Anchor',
    'test@gmail.com',
    $secret_key
);
$writer = new Writer(
    new ImageRenderer(
        new RendererStyle(200),
        new SvgImageBackEnd() // imagick couldn't be rendered requires imagick
    )
);

$qrcode_image = $writer->writeString($g2faUrl);

