<?php

namespace Tests\Unit;

use App\SecureShellCommand;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecureShellCommandTest extends TestCase
{
    public function test_it_formats_the_shell_command()
    {
        $command = new SecureShellCommand('whoami', '~/.ssh/deploymentKey');

        $this->assertEquals(
            'ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -i ~/.ssh/deploymentKey -p 22 root@127.0.0.1 whoami',
            (string)$command
        );
    }
}
