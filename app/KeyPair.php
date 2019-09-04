<?php

namespace App;

use App\Concerns\EncryptsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KeyPair extends Model
{
    use EncryptsAttributes;

    protected $fillable = [
        'owner_type',
        'owner_id',
        'encryption_key',
        'public_key',
        'private_key'
    ];

    /**
     * The owner of this key pair
     *
     * @return MorphTo
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * Set the public key
     *
     * @param string $value
     * @throws EncryptException
     * @return void
     */
    public function setPublicKeyAttribute($value)
    {
        $this->attributes['public_key'] = $this->encrypter()->encrypt(
            $value
        );
    }

    /**
     * Retrieve the unencrypted public key
     *
     * @param string $encryptedValue
     * @throws DecryptException
     * @return string
     */
    public function getPublicKeyAttribute($encryptedValue)
    {
        return $this->encrypter()->decrypt(
            $encryptedValue
        );
    }

    /**
     * Set the private key
     *
     * @param string $value
     * @throws EncryptException
     * @return void
     */
    public function setPrivateKeyAttribute($value)
    {
        $this->attributes['private_key'] = $this->encrypter()->encrypt(
            $value
        );
    }

    /**
     * Retrieve the unencrypted private key
     *
     * @param string $encryptedValue
     * @throws DecryptException
     * @return string
     */
    public function getPrivateKeyAttribute($encryptedValue)
    {
        return $this->encrypter()->decrypt(
            $encryptedValue
        );
    }
}
