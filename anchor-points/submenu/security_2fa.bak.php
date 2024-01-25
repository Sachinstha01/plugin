<?php

require_once __DIR__ . '/../config/wp2fa.php';
// include_once(__DIR__ . "/../config/wp2facodes.php");

$newInstance =  new Google2FAService();

// $recoveryCode=new RecoveryCodes(); 
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
        echo $newInstance->generateQRCodeImage('Anchor', 'test@gmail.com', $secret_key);
        $CurrentTimeStamp = $newInstance->getTimeStamp();

        ?>
        <br>
        <?php echo $secret_key;
        ?>
        <div class="qr-input">
            <!-- localhost/wpPlugin/wordpress/config/wp2fa.php -->
            <!-- http://localhost/wpPlugin/wordpress/wp-admin/config/wp2fa.php -->
            <form method="post" action="../wp-content/plugins/wordpress-security/config/testOTP.php">
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

            <br>
            <br>
            <div class="recoveryCode">
                <?php
               
                //recovery codes here
                use PragmaRX\Recovery\Recovery as code;

                $codes = new code();
                //  var_dump(code->generate()->toJson());
                $keys = $codes->mixedcase()->alpha()->setBlockSeparator("-")->toJson();
                $finalKeys= json_decode($keys, true);
                // var_dump(json_decode($keys))
                // var_dump($recoveryCode);
                ?>
                <br><br>
                <?php

                // The original string you want to encode
                // $originalString = "Hello";
                $originalString = $keys;
                // Generate a random key (you should securely store and manage this key)
                $key = sodium_crypto_secretbox_keygen();

                // Generate a random nonce (number used once)
                $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

                // Encrypt the string
                $encryptedString = sodium_crypto_secretbox($originalString, $nonce, $key);

                // Convert binary data to base64
                $base64EncryptedString = sodium_bin2base64($encryptedString, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);

                // To store or transmit the encrypted string, concatenate the nonce and the base64-encoded ciphertext
                $combined = $nonce . $base64EncryptedString;

                // To decrypt the string later, extract the nonce and base64-encoded ciphertext
                $nonce = substr($combined, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
                $base64EncryptedString = substr($combined, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

                // Convert base64 to binary data
                $ciphertext = sodium_base642bin($base64EncryptedString, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);

                // Decrypt the string
                $decryptedString = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);

                // Output the results
                echo esc_html__("Original String:\n $originalString\n");
            ?>
              <strong>  
                 <?php echo esc_html__("Encrypted String: $base64EncryptedString\n"); ?>
</strong>
<?php
                echo esc_html__("Decrypted String: $decryptedString\n");
                //
                ?>
                <br><br>
            </div>
            <script>
                // script to show the 2FA configuration on Click
                $(document).ready(function() {
                    $('#Activate2FA').on('click', function() {

                        //OTP handle assigned in the url variable
                        var url = 'testingsite.php'; //test site
                        var data = $('#Activate2FA').val();
                        // Send the POST request using jQuery AJAX
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: data,
                            success: function(response) {
                                console.log('2FA Activated');
                            },
                            error: function(error) {
                                console.error('Error:', error);
                            }
                        });
                    });
                });
            </script>

            <!-- 2FA Button -->
        </div>
    </div>

</body>

</html>