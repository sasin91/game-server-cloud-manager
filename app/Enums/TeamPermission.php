<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TeamPermission extends Enum
{
    const DeleteServerInCloud = 'delete server in cloud';
    const UpdateServerInCloud = 'update server in cloud';
    const CreateServerInCloud = 'create server in cloud';
    const CreateCloud = 'create cloud';
    const CreateTeamInvitations = 'create team invitations';
    const UpdateProject = 'update project';
}
