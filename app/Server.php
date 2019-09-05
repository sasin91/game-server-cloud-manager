<?php

namespace App;

use App\Events\ServerCreated;
use App\Events\ServerDeleted;
use App\Events\ServerUpdated;
use App\Events\ServerRestored;
use App\Events\ServerRestoring;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property Environment $environment
 * @property Cloud $cloud
 */
class Server extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'environment_id',
        'cloud_id',
        'status',
        'image',
        'private_address',
        'public_address',
        'provider_id'
    ];

    protected $dispatchesEvents = [
        'created' => ServerCreated::class,
        'updated' => ServerUpdated::class,
        'deleted' => ServerDeleted::class,
        'restoring' => ServerRestoring::class,
        'restored' => ServerRestored::class
    ];

    /**
     * The servers environment variables
     *
     * @return BelongsTo
     */
    public function environment()
    {
        return $this->belongsTo(Environment::class);
    }

    /**
     * The cloud this server was deployed in
     *
     * @return BelongsTo
     */
    public function cloud()
    {
        return $this->belongsTo(Cloud::class);
    }

    /**
     * The key pairs deployed to this server
     *
     * @return BelongsToMany
     */
    public function keyPairs()
    {
        return $this->morphMany(KeyPair::class, 'owner');
    }

    /**
     * The deployments on this server
     *
     * @return HasMany
     */
    public function deployments()
    {
        return $this->hasMany(Deployment::class);
    }
}
