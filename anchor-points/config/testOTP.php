<?php

$cwd= getcwd();
var_dump($cwd);
$dir= $cwd.'/wp2fa.php';
var_dump($dir);

include_once($cwd."\wp2fa.php");
defined('ABSPATH') or die('Error ABSPATH not set');

// D:\xampp\htdocs\wpPlugin\wordpress\wp-content\plugins\wordpress-security\config"
$newInstance =  new Google2FAService();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['qrcode'])) {
        $qrcodeValue = $_POST['qrcode'];
        //
        /*
            @param bool| $valid
            @param int | 1 key == 30 (secs) past and future 
            $window = 4;
        */

        //https://github.com/antonioribeiro/google2fa/issues/189
        // $valid = $google2fa->verifyKey(Google2FAService::setKey($testInstance), $secret, $google2fa->google_ts);
        // $valid = $google2fa->verifyKeyNewer(Google2FAService::setKey($testInstance), $secret, $google2fa->google_ts);
        $valid = $google2fa->verifyKeyNewer(Google2FAService::setKey($testInstance), $secret, Google2FAService::getTimeStamp());

        var_dump($valid);
        //valid not working
        if ($valid !== false) {
            echo json_encode(['success' => true, 'message' => 'OTP validation working', 'qrcode' => $qrcodeValue]);
        }
        else{
            echo json_encode(['success' => false, 'message' => 'OTP ERROR']);
        }
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
