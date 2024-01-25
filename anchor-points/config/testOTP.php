<?php

$cwd = getcwd();
var_dump($cwd);
// var_dump($dir);
// defined('ABSPATH') or die('Error ABSPATH not set');
include_once(__DIR__ . '\wp2fa.php');
// include_once($test_dir);
// die(json_encode(['message'=>'wp2fa.php is not found']));
$newInstance =  new Google2FAService();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['qrcode'])) {
        // $qrcodeValue = $_POST['qrcode'];
        // $qrcode=null;
        $qrCodeValue = isset($_POST['qrcode']);
        $qrCodeValue = preg_replace("/[^0-9]/", "", $qrCodeValue);
        // Function to sanitize input
        function sanitizeInput($qrCodeValue)
        {
            $input = trim($qrCodeValue); // Remove leading and trailing whitespaces
            $input = htmlspecialchars($qrCodeValue); // Convert special characters to HTML entities
            return $input;
        }

        function validateQRCode($qrCodeValue)
        {
            $qrcode= $qrCodeValue;
            // Remove any non-numeric characters
            // Check if the length is between 1 and 6 characters
            if (strlen($qrcode) < 1 || strlen($qrcode) > 6) {
                return false; // Invalid length
            }
            return true; // Valid QR code
        }
        // echo json_encode(['Empty OTP field']);
        if (validateQRCode($qrCodeValue)!== false ) {
            var_dump($newInstance->getTimeStamp());
            // var_dump($qrcodeValue);
            $valid = $newInstance->keyVerifcation($qrcodeValue);
            var_dump($valid);
        }else{
            echo "Invalid QR code";
        }

        //
        /*
            @param bool| $valid
            @param int | 1 key == 30 (secs) past and future 
            $window = 4;
        */

        //https://github.com/antonioribeiro/google2fa/issues/189
        // $valid = $google2fa->verifyKey(Google2FAService::setKey($testInstance), $secret, $google2fa->google_ts);
        // $valid = $google2fa->verifyKeyNewer(Google2FAService::setKey($testInstance), $secret, $google2fa->google_ts);
        // $valid = $newInstance->google2fa->verifyKeyNewer(Google2FAService::setKey($testInstance), $secret, Google2FAService::getTimeStamp());

        //valid not working
        // if ($valid !== false) {
        //     echo json_encode(['success' => true, 'message' => 'OTP validation working', 'qrcode' => $qrcodeValue]);
        // }
        // else{
        //     echo json_encode(['success' => false, 'message' => 'OTP ERROR']);
        // }
        //
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid']);
        exit;
    }

    // if (isset($_POST['qrcode'])) {
    //     $qrcodeValue = $_POST['qrcode'];

    //     echo json_encode(['success'=>true,'message'=>'qrcode is setuped','qrcode'=> $qrcodeValue]);
    // }else{
    //     echo json_encode(['success'=>false,'message'=>'qrcode error']);
    // }
}
