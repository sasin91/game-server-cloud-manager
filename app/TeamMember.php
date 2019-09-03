<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamMember extends Pivot
{
    protected $table = 'team_members';

    use RefreshDatabase;

    protected $fillable = [
        'team_id',
        'user_id',
        'permissions'
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
