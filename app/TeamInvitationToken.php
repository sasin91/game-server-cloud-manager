<?php

namespace App;

use Illuminate\Support\Str;

class TeamInvitationToken
{
    /**
     * Generate a token for team invitations
     *
     * @return string
     */
    public static function make()
    {
        return Str::random();
    }
}
