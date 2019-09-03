<?php

namespace Tests\Unit\Jobs;

use App\Cloud;
use App\Jobs\DeleteServerInCloud;
use App\Server;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

class DeleteServerInCloudTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_job_sends_a_delete_request_to_the_cloud_provider()
    {
        $this->markTestSkipped("The mock is properly resolved in the test but not in the job despite being called directly?...");

        $this->mock('DigitalOcean', function (MockInterface $mock) {
            $mock->expects('deleteServer')->with('1234abc')->andReturnNull();
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
