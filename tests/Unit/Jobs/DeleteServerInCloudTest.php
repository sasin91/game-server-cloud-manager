<?php

namespace Tests\Unit\Jobs;

use Mockery;
use App\Cloud;
use App\Server;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Jobs\DeleteServerInCloud;
use App\CloudProviders\DigitalOcean;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteServerInCloudTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_job_sends_a_delete_request_to_the_cloud_provider()
    {
        $this->app->bind(DigitalOcean::class, function ($app) {
            $mock = Mockery::mock(DigitalOcean::class);
            $mock->shouldDeferMissing();
            $mock->expects('deleteServer')->with('1234abc');

            return $mock;
        });

        $cloud = factory(Cloud::class)->create(['provider' => 'DigitalOcean']);
        $server = factory(Server::class)->create([
            'cloud_id' => $cloud->id,
            'provider_id' => '1234abc'
        ]);

        $job = new DeleteServerInCloud($server);

        $this->app->call([$job, 'handle']);
    }
}
