<?php

namespace App;

use App\Concerns\EncryptsAttributes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Environment extends Model
{
    use EncryptsAttributes;

    private $_decryptedVariables;

    protected $fillable = [
        'encryption_key',
        'variables'
    ];

    /**
     * Set the environment variables
     *
     * @param array $variables
     * @return void
     */
    public function setVariablesAttribute($variables)
    {
        $this->attributes['variables'] = $this->encrypter()->encrypt(
            EnvironmentVariables::stringify($variables)
        );
    }

    public function variables()
    {
        if ($this->_decryptedVariables) {
            return $this->_decryptedVariables;
        }

        return $this->_decryptedVariables = EnvironmentVariables::collect($this->encrypter()->decrypt($this->variables));
    }

    public function variable($key, $default = null)
    {
        return $this->variables()->first(function ($value, $variable) use ($key) {
            return Str::upper($variable) === Str::upper($key);
        }, $default);
    }
}
