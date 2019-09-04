<?php

namespace App\Jobs;

use App\Server;
use App\ServerConfiguration;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class CreateServerInCloud implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The server we're creating in the cloud
     *
     * @var Server
     */
    public $server;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($server)
    {
        $this->server = $server;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->server
            ->cloud
            ->provider()
            ->createServer(function (ServerConfiguration $server) {
                $server->image = $this->server->image;

                if ($this->server->private_address) {
                    $server->privateNetworking = true;
                }

                if (! is_numeric($this->server->public_address)) {
                    $server->name = $this->server->public_address;
                } else {
                    $server->name = Uuid::uuid4();
                }
            }, $this->server->keyPairs()->pluck('public_key'));
    }
}
