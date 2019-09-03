<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ServerKeyPair extends Pivot
{
    protected $fillable = [
        'server_id',
        'key_pair_id'
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function keyPair()
    {
        return $this->belongsTo(KeyPair::class);
    }
}
