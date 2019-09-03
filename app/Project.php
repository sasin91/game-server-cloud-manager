<?php

namespace App;

use App\VersionControl;
use App\Contracts\VersionControl as versionControlContract;
use App\Events\ProjectCreated;
use App\Events\ProjectDeleted;
use App\Events\ProjectUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'environment_id',
        'repository_url',
        'version_control'
    ];

    protected $dispatchesEvents = [
        'created' => ProjectCreated::class,
        'updated' => ProjectUpdated::class,
        'deleted' => ProjectDeleted::class
    ];

    /**
     * Team behind this project
     *
     * @return BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * The environment variables for this project.
     * Ie. Auth tokens and so on.
     *
     * @return BelongsTo
     */
    public function environment()
    {
        return $this->belongsTo(Environment::class);
    }

    /**
     * The deployments of this project
     *
     * @return HasMany
     */
    public function deployments()
    {
        return $this->hasMany(Deployment::class, 'project_id');
    }

    /**
     * Get an instance of the VCS used for this project
     *
     * @return VersionControlContract
     */
    public function versionControl()
    {
        return VersionControl::make($this->version_control);
    }
}
