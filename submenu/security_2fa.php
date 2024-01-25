<?php
require_once __DIR__ . '/../config/wp2fa.php';
include_once(__DIR__ . "/../config/wpRecoveryCodes.php");

$newInstance =  new Google2FAService();
// use Google2FAService;
$rCodes = new RecoveryCodes();
$recCodes = $rCodes->generateRecoveryCodes(); // variable to play with
$decryptedCodes = $rCodes->encryptString();
$encryptedCodes = $rCodes->decryptString();


// Recovery code simplification
preg_match_all('/\b([A-Za-z0-9]+)\b/', $recCodes, $matches);
$test_rec = ''; // used into send at the database
$reversed = array_map(function ($str) {
    return $str;
}, $matches[1]);

// Convert to the specified format
$formatted = array_map(function ($str) {
    return chunk_split($str, 4, ' ');
}, $reversed);

// Output the result
foreach ($formatted as $line) {
    // echo trim($line) . PHP_EOL;
    $test_rec .= trim($line) . ' ';
    print_r("\r\n");
}
//
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</head>

<body>
    <div class="wrap">
        <h1>
            Security 2FA Configurations
        </h1>
        <h5>
        </h5>
        <div id="qr-image">
            <?php
            //  var_dump($newInstance->generateSecretKey(16));
            ?>
        </div>
        <?php
        // echo $qrcode_image;
        $secret_key = $newInstance->generateSecretKey(32);
        echo $newInstance->generateQRCodeImage('AnchorPoints', '', $secret_key);
        $CurrentTimeStamp = $newInstance->getTimeStamp();

        ?>
        <br>
        <?php echo $secret_key;
        ?>
        <div class="qr-input">
            <form method="post" action="../wp-content/plugins/wordpress-security/config/testOTP.php">
                <!-- <form method="post" action="#"> -->
                <label>QR codes</label>
                <input type="text" name="qrcode" id="">
                <input type="submit" value="Submit">
            </form>
            <br><br>
            <h4>Timestamp used to create the QR code value</h4>
            <?php
            var_dump($CurrentTimeStamp);
            ?>
            <br>
            <!-- 2FA Button  -->
            <!-- <button id="Activate2FA">Activate 2FA</button> -->
            <input type="button" value="Activate" id="Activate2FA">
            <input type="button" value="Download OTP" id="DownloadCodes">
            <input type="button" value="Save OTP configuration" id="saveOTP">

            <br>
            <br>
            <div class="recoveryCode">
                <br>
                <strong>TEST Rec: </strong>
                <?php
                // var_dump($test_rec);
                echo $test_rec;
                ?>
                <br>
                <strong>Encrypted Code</strong>
                <?php
                var_dump($decryptedCodes);
                ?>
                <br>
                <strong> Decrypted String: </strong>
                <?php
                var_dump($encryptedCodes);
                ?>
                <br><br>

            </div>
            <script>
                $(document).ready(function() {
                    // Recovery Codes download
                    $('#DownloadCodes').on('click', function() {
                        downloadRecoveryCodes();
                    });
                    $('#saveOTP').on('click', function() {
                        sendRecoveryCodes();
                    });

                    // 2FA Settings handler
                    function downloadRecoveryCodes() {
                        $.ajax({
                            url: window.location.href,
                            dataType: 'text',
                            success: function() {
                                var recCodes = '<?php echo $test_rec; ?>';
                                // console.log('2FA Activated');
                                var blob = new Blob([recCodes], {
                                    type: 'text/plain'
                                });
                                console.log("Button Clicked");
                                var download = confirm("Download the OTP codes? ");
                                if (download) {
                                    var link = document.createElement('a');
                                    link.href = window.URL.createObjectURL(blob);
                                    link.download = 'recovery_codes.txt';
                                    link.click();
                                }
                                // var link = document.createElement('a');
                                // link.href = window.URL.createObjectURL(blob);
                                // link.download = 'recovery_codes.txt';
                                // link.click();
                                // console.log();
                            },
                            error: function(error) {
                                console.log("Error ".error)
                            }
                        });
                    }

                    function sendRecoveryCodes() {

                        // var recoveryCodes = 'echo $recCodes';
                        var hello = "Hello";
                        $.ajax({
                            type: "POST",
                            // url: "/wpPlugin/wordpress/wp-admin/admin-ajax.php",
                            url: recovery_codes_security.ajaxurl,
                            dataType: 'json',
                            data: {
                                'action':'handle_recovery_codes',
                                'msg': hello,
                            },
                            success: function(response) {
                                console.log(response);
                            },
                            error: function(error) {
                                console.log("Error : ", error);
                            }
                        });
                    }
                });
            </script>

            <!-- 2FA Button -->
        </div>
    </div>

</body>

</html>