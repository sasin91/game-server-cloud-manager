<?php

namespace App\Actions;

use App\Server;
use App\Jobs\DeleteServerInCloud;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeleteServer
 * @package App\Actions
 */
class DeleteServer
{
    /**
     * Run the action.
     *
     * @param Server|Model $server
     * @return void
     */
    public function execute($server)
    {
        if ($server->delete()) {
            \dispatch(new DeleteServerInCloud(
                $server
            ));
        }
    }
}
