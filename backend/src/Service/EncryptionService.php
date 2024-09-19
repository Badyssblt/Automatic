<?php

namespace App\Service;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

class EncryptionService
{
    private $key;

    public function __construct(string $key)
    {
        $this->key = Key::loadFromAsciiSafeString($key);
    }

    public function encrypt(string $data): string
    {
        return Crypto::encrypt($data, $this->key);
    }

    public function decrypt(string $encryptedData): string
    {
        return Crypto::decrypt($encryptedData, $this->key);
    }
}
