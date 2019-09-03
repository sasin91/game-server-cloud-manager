<?php

namespace App\Actions;

use App\Server;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UpdateServer
 * @package App\Actions
 */
class UpdateServer
{
    /**
     * Run the action.
     *
     * @param Server|Model $server
     * @param array $parameters
     * @return Server|Model
     */
    public function execute($server, array $parameters)
    {
        $server->update($parameters);

        return $server;
    }
}
