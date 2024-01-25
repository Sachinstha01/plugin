<?php

use PragmaRX\Recovery\Recovery as RecoveryCode;
use ParagonIE\ConstantTime\Base64;

class RecoveryCodes
{
    private $reCode;
    private $encryptedString;
    public function generateRecoveryCodes(): string
    {
        $recoveryCode = new RecoveryCode();
        $this->reCode = $recoveryCode->mixedcase()->setCount(8)->setChars(4)->alpha()->setBlockSeparator(" ")->toJson();

        return $this->reCode;
    }
    //encryption method
    public function encryptString(): string
    {
        return $this->encryptedString = Base64::encode($this->reCode);
    }

    //decryption methods
    public function decryptString(): string
    {
        return Base64::decode($this->encryptedString);
    }
}
