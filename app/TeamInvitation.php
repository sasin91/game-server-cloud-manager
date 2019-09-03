<?php

namespace App;

use App\Events\TeamInvitationSent;
use App\Events\TeamInvitationCreated;
use App\Events\TeamInvitationDeleted;
use App\Events\TeamInvitationUpdated;
use App\Events\TeamInvitationAccepted;
use App\Events\TeamInvitationDeclined;
use Illuminate\Database\Eloquent\Model;
use App\Concerns\GeneratesInvitationToken;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as ELoquentBuilder;
use App\Notifications\TeamInvitation as TeamInvitationNotification;

/**
 * @method static EloquentBuilder|QueryBuilder onlyEligable()
 */
class TeamInvitation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'creator_id',
        'recipient_id',
        'token',
        'accepted_at',
        'sent_at'
    ];

    protected $dispatchesEvents = [
        'created' => TeamInvitationCreated::class,
        'updated' => TeamInvitationUpdated::class,
        'deleted' => TeamInvitationDeleted::class,
        'sent' => TeamInvitationSent::class,
        'accepted' => TeamInvitationAccepted::class,
        'declined' => TeamInvitationDeclined::class
    ];

    public function scopeOnlyEligable($query)
    {
        return $query
        ->withoutTrashed()
        ->whereNull('accepted_at');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function send()
    {
        $this->recipient->notify(
            new TeamInvitationNotification(
                $this->team
            )
        );

        $this->update(['sent_at' => $this->freshTimestampString()]);

        $this->fireModelEvent('sent', false);

        return $this;
    }

    public function markAsAccepted()
    {
        $this->update([
            'accepted_at' => $this->freshTimestampString()
        ]);

        $this->delete();

        $this->fireModelEvent('accepted', false);

        return $this;
    }

    public function markAsDeclined()
    {
        $this->delete();

        $this->fireModelEvent('declined', false);

        return $this;
    }
}
