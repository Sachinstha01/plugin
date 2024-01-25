<?php
// key generation script

use PragmaRX\Google2FAQRCode\Google2FA;
use PragmaRX\Google2FA\Support\Constants;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

// $dir = getcwd();
// var_dump(__DIR__);
// die();
// die (json_encode(['message'=>'current directory '. $dir]));
// include_once($dir."/../vendor/autoload.php");
include_once(__DIR__ ."/../../../../vendor/autoload.php");
class Google2FAService
{
    private $google2fa;
    // private $secret_key;
    // private static $testKey;
    public function __construct()
    {
        
        $this->google2fa = new Google2FA();
        $this->google2fa->setAlgorithm(Constants::SHA512); // Encryption type
        // $this->secret_key = $this->generateSecretKey(32);
        // $this->generateQRCodeImage($this->companyName, $this->companyEmail, $this->secret_key);
        // $this->timeStamp();
    }

    //this for testing only
    // public static function setKey($instance) : string
    // {
    //     // $instance = new Google2FA();
    //     self::$testKey= $instance->generateSecretKey(16);
    //     return self::$testKey;
    // }

    //Generate SecretKey
    public function generateSecretKey($length) : string
    {
        return $this->google2fa->generateSecretKey($length);
    }

    public function generateQRCodeImage($companyName, $companyEmail, $secretKey) : string
    {
        // $qrCodeUrl= $qrCodeUrlfunction();
        $qrCodeUrl = $this->google2fa->getQRCodeUrl($companyName, $companyEmail, $secretKey);
        // 
        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd() // imagick couldn't be rendered requires imagick
            )
        );

        return $writer->writeString($qrCodeUrl);
    }

    // public function updateQRtimeStamp(){
    // }
    /* 
        * @param bool $valid
        * @param int $timestamp
    */
    //get current time
    public static function getTimeStamp() : int
    {
        // return $this->google2fa->getTimeStamp();
        $current_timestamp = time();
        return $current_timestamp;
    }
    // $valid = $newInstance->google2fa->verifyKeyNewer(Google2FAService::setKey($testInstance), $secret, Google2FAService::getTimeStamp());
    // $google2fa->verifyKey($user->google2fa_secret, $secret);
    //
    // $timestamp = $google2fa->verifyKeyNewer($user->google2fa_secret, $secret, $user->google2fa_ts);

    //OTP verification  method
    public function keyVerifcation($secret): bool
    {

        $generatedSecret= $this->generateSecretKey(16);
        $currentTime= $this->getTimeStamp();
        // $timeStamp=$this->google2fa->verifyKeyNewer($generatedSecret,$secret,$currentTime);
        // $timeStamp=$this->google2fa->verifyKey($generatedSecret,$secret,4);
        $timeStamp=$this->google2fa->verifyKeyNewer($generatedSecret,$secret,$currentTime,4);
     
        return $timeStamp;
    //    $valid= false;
    //     if($timeStamp==true){
    //         $valid= true;
    //     }else{
    //         $valid=false;
    //     }
    //     return $valid;
        
    }
 
}
