<?php

namespace App\Events;

use App\Deployment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeploymentDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $model;

    /**
     * Create a new event instance.
     *
     * @param Deployment $model
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }
}
