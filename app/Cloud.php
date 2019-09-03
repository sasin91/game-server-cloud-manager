<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\CloudProviderNotFound;
use Illuminate\Database\Eloquent\Collection as DatabaseCollection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Contracts\CloudProvider as CloudProviderContract;
use App\Events\CloudCreated;
use App\Events\CloudDeleted;
use App\Events\CloudRestored;
use App\Events\CloudUpdated;

/**
 * @property Team $team
 * @property Environment $environment
 * @property DatabaseCollection $servers
 */
class Cloud extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'environment_id',
        'provider',
        'private_network',
        'address'
    ];

    protected $dispatchesEvents = [
        'created' => CloudCreated::class,
        'updated' => CloudUpdated::class,
        'deleted' => CloudDeleted::class,
        'restored' => CloudRestored::class
    ];

    /**
     * The team that is responsible for this cloud
     *
     * @return BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * The cloud provider environment
     *
     * @return BelongsTo
     */
    public function environment()
    {
        return $this->belongsTo(Environment::class);
    }

    /**
     * The deployed server instances within this cloud
     *
     * @return HasMany
     */
    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    /**
     * Get an instance of the CloudProvider for this cloud
     *
     * @throws CloudProviderNotFound
     * @return CloudProviderContract
     */
    public function provider()
    {
        return CloudProvider::make($this);
    }
}
