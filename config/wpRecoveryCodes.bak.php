<?php

use PragmaRX\Recovery\Recovery as RecoveryCode;
use SodiumException as SodEx;

//Recovery codes for the TOTP 
class RecoveryCodes
{
    private $ciphertext;
    private $nonce;
    private $key;
    private $reCode;
    // private $normalString;
    public function __construct()
    {
    }

    public function generateRecoveryCodes(): string
    {
        $recoveryCode = new RecoveryCode();
        $this->reCode = $recoveryCode->mixedcase()->setCount(4)->setChars(4)->alpha()->setBlockSeparator(" ")->toJson();
        // $finalKeys = json_decode($this->reCode, true);

           return $this->reCode;
        // return $finalKeys;
    }
    //encryption method
    public function encryptString(): string
    {
        $this->key = sodium_crypto_secretbox_keygen();
        $this->nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        //sodium_crypto_secretbox(string $message, string $nonce, string $key): string

        // $encryptedString = sodium_crypto_secretbox($message, $this->nonce, $this->key); //message === generatedRecovery Code
        $encryptedString = sodium_crypto_secretbox($this->reCode, $this->nonce, $this->key);

        $base64EncryptedString = sodium_bin2base64($encryptedString, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
        $combined = $this->nonce . $base64EncryptedString;
        // $combined = $this->nonce . $encryptedString;

        // Try catch block
        // try {
        //     $base64EncryptedString = sodium_bin2base64($encryptedString, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
        //     $combined = $this->nonce . $base64EncryptedString;
        // } catch (SodEx $error) {
        //     echo "Error Occured in conversion". $error->getMessage();
        // }


        //Encryption using Paragonie/Encoding
        $this->nonce = substr($combined, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $base64EncryptedString = substr($combined, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        // Convert base64 to RAW binary data // for Database storage
        $this->ciphertext = sodium_base642bin($base64EncryptedString, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
        $encryptedText = $this->ciphertext;
        return $encryptedText;
    }

    //decryption methods
    public function decryptString(): string
    {
        $decryptedString = sodium_crypto_secretbox_open($this->ciphertext, $this->nonce, $this->key);
        return $decryptedString;
    }

    // Key storing handler method
}
