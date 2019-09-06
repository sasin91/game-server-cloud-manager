<?php

namespace Tests\Unit\Scripts;

use App\Scripts\AzerothCore\CreateGameAccountThroughTelnet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateGameAccountThroughTelnetTest extends TestCase
{
    public function testItRendersTheScript()
    {
        $script = new CreateGameAccountThroughTelnet('hello', 'world');
        $script->address = '123.4.5.6';
        $script->port = 1234;
        $script->adminName = 'wowAdmin';
        $script->adminPassword = 'secret';

        $rendered = $script->render();

        self::assertStringContainsString('hello', $rendered);
        self::assertStringContainsString('world', $rendered);
        self::assertStringContainsString('123.4.5.6', $rendered);
        self::assertStringContainsString('1234', $rendered);
        self::assertStringContainsString('wowAdmin', $rendered);
        self::assertStringContainsString('secret', $rendered);
    }
}
