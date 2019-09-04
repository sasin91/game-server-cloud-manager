<?php

namespace Tests\Unit\Jobs;

use Mockery;
use App\Cloud;
use App\Server;
use Tests\TestCase;
use App\Jobs\CreateServerInCloud;
use App\CloudProviders\DigitalOcean;
use App\Environment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateServerInCloudTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_job_sends_a_post_request_to_the_cloud_provider()
    {
        $this->app->bind(DigitalOcean::class, function ($app) {
            $mock = Mockery::mock(DigitalOcean::class);
            $mock->shouldDeferMissing();
            $mock->expects('createServer')->once()->andReturn(1);

            return $mock;
        });

        $environment = factory(Environment::class)->create([
            'variables' => ['provider_token' => '1234abcdef']
        ]);

        $cloud = factory(Cloud::class)->create([
            'environment_id' => $environment->id,
            'provider' => 'DigitalOcean'
        ]);

        $server = factory(Server::class)->create([
            'cloud_id' => $cloud->id
        ]);

        $job = new CreateServerInCloud($server);

        $this->app->call([$job, 'handle']);
    }
}
