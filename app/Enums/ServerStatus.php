<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ServerStatus extends Enum
{
    const ONLINE = 'Online';
    const OFFLINE = 'Offline';
    const DEPLOYING = 'Deploying';
    const PROVISIONING = 'Provisioning';
}
