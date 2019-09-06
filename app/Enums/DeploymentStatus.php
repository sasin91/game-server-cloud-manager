<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class DeploymentStatus extends Enum
{
    const EXECUTING = 'Executing';
    const EXECUTED = 'Executed';
    const FAILED = 'Failed';
    const SUCCESS = 'Success';
}
