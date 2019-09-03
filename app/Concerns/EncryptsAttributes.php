<?php

namespace App\Concerns;

use function config;
use function base64_decode;
use function base64_encode;
use Illuminate\Encryption\Encrypter;

trait EncryptsAttributes
{
    /**
     * Generate a new encryption key
     *
     * @return string
     */
    public function generateEncryptionKey()
    {
        return base64_encode(Encrypter::generateKey(config('app.cipher')));
    }

    /**
     * The encryptor instance
     *
     * @return Encrypter
     */
    public function encrypter()
    {
        if (blank($this->encryption_key)) {
            $this->encryption_key = $this->generateEncryptionKey();
        }

        return new Encrypter(
            base64_decode($this->encryption_key),
            config('app.cipher')
        );
    }
}
