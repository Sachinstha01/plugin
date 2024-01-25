<?php
require_once __DIR__ . '/../config/wp2fa.php';
include_once(__DIR__ . "/../config/wpRecoveryCodes.php");

$newInstance =  new Google2FAService();
// use Google2FAService;
$rCodes = new RecoveryCodes();
$recCodes = $rCodes->generateRecoveryCodes(); // variable to play with
// $decryptedCodes = $rCodes->encryptString();
// $encryptedCodes = $rCodes->decryptString();

// Recovery code simplification

preg_match_all('/\b([A-Za-z0-9]+)\b/', $recCodes, $matches);
// preg_match_all('/\b([A-Za-z0-9]+)\b/', $rCodes->generateRecoveryCodes(), $matches);
$decryptedCodes = $rCodes->encryptString();
$encryptedCodes = $rCodes->decryptString();

$test_rec = ''; // used into send at the database
$reversed = array_map(function ($str) {
    return $str;
}, $matches[1]);

// Convert to the specified format
$formatted = array_map(function ($str) {
    return chunk_split($str, 4, ' ');
}, $reversed);

// preg_match_all('/\b([A-Za-z0-9]+)\b/', $rCodes->generateRecoveryCodes(), $matches);
foreach ($formatted as $line) {
    $line = trim($line);

    for ($i = 0; $i < strlen($line); $i += 8) {
        $chunk = substr($line, $i, 8);
        // Append to $test_rec with a hyphen
        $test_rec .= $chunk . ' ';
        // Print the chunk on the same line
        // print_r( $chunk);
        print("\n");
        echo PHP_EOL;
    }
}

$decryptedCodes = $rCodes->encryptString();
$encryptedCodes = $rCodes->decryptString();


// Output the result
// foreach ($formatted as $line) {
//     // echo trim($line) . PHP_EOL;
//     $test_rec .= trim($line) . '-';
//     print_r("\r\n");
// }
//
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title></title>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        /* Custom Styling */

        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@100;400;500;700&display=swap');

        * {
            box-sizing: border-box;
            margin: 0px;
            padding: 0px;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: #ccc;
            font-size: 18px;
        }

        .qr_image_header,
        .qr_recovery_header {
            font-weight: 800;
            border-bottom: 1px solid #ccc;
            padding-bottom: 20px;
        }

        .wrap {
            display: flex;
            gap: 20px;
            width: 90%;
            margin: 0px auto;
        }

        .qr_code>img {
            height: 250px;
        }

        .qr_image,
        .qr-recovery {
            background-color: #fff;
        }

        .qr_image {
            padding: 20px;
        }

        .qr_generated_result {
            margin-bottom: 10%;
        }

        .qr_image {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .qr_generated_result_btn {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #000;
            font-size: 18px;
        }

        .qr_details {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
        }

        .qr-recovery {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 20px;
        }

        .qr_recovery_results {
            display: flex;
            flex-direction: column;
            gap: 30px;
            align-items: center;
            border-bottom: 1px solid #ccc;
            width: 25%;
            padding-bottom: 20px;
            margin: 0px auto;
        }

        .qr_btn_download1 {
            padding: 10px 20px;
            font-size: 18px;
            background-color: #fff;
            border: 2px solid #ccc;
            border-radius: 8px;
        }

        .qr_authenticator_code {
            font-size: 25px;
            border-radius: 8px;
            color: #ccc;
            border: 1px solid #ccc;
            padding: 10px 20px;
            display: flex;
            justify-content: center;
            font-weight: 500;
        }

        .qr-authenticator-main {
            display: flex;
            justify-content: center;
        }

        .qr_activate_buttton1 {
            text-transform: uppercase;
            font-weight: 400;
            color: #ccc;
            font-size: 18px;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            background-color: #fff;
        }

        .qr_activate_text {
            font-size: 18px;
            font-weight: 400;
        }

        .qr_activate {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ccc;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="qr_image">
            <div class="qr_image_header">1. Scan Code or Enter Key</div>
            <div class="qr_text_image_text">
                Scan the code below with your authentication app to add this account.
                Some authenticator apps also allow tou to type in the text version
                instead.
            </div>
            <div class="qr_details">
                <div class="qr_code">
                    <?php
                    $secret_key = $newInstance->generateSecretKey(32);
                    echo $newInstance->generateQRCodeImage('AnchorPoints', '', $secret_key);
                    // $CurrentTimeStamp = $newInstance->getTimeStamp();
                    ?>
                </div>
                <div class="qr_generated_result">
                    <button class="qr_generated_result_btn">
                        <?php
                        echo $secret_key;
                        ?>
                    </button>
                </div>
            </div>
        </div>
        <div class="qr-recovery">
            <div class="qr_recovery_header">
                2. Enter Code from Authenticaecovery Codes
            </div>
            <div class="qr_recovery_content">
                Dowload Recovery codes <i>Optional</i> <br /><br />
                Use on of these 5 codes to log in if you lose access to your
                authenticator device. Codes are 16 characters long plus optional
                spaces. Each one may be used only once.
            </div>
            <div class="qr_recovery_codes">
                <div class="qr_recovery_results">
                    <div class="qr_codes-rec">
                        <?php
                        print_r ($recCodes);
                        ?>
                    </div>
                    <div class="qr_button_download" id="download">
                        <input type="button" value="Download OTP" class="qr_btn_download1" id="DownloadCodes">
                    </div>
                </div>
            </div>
            <div class="qr_recovery_text">
                Enter the code from your authenticator app below to verify and
                activate two-factor authentication this account.
            </div>
            <div class="qr-authenticator-main">
                <div class="qr_authenticator_code">
                    <label for="">OTP here</label>
                    <input type="text" value="">
                </div>
            </div>
        </div>
    </div>
</body>

</html>




<script>
    $(document).ready(function() {
        // Recovery Codes download
        // $('#DownloadCodes').on('click', function() {
        $('#download').on('click', function() {
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
                    'action': 'handle_recovery_codes',
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

</html>