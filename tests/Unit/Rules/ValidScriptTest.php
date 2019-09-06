<?php

namespace Tests\Unit\Rules;

use Tests\TestCase;
use App\Rules\ValidScript;
use App\Script;
use App\Scripts\AzerothCore\DeployAzerothCoreUsingDocker;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class ValidScriptTest extends TestCase
{
    /**
     * The rule we're checking
     *
     * @var ValidScript
     */
    protected $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = $this->app->make(ValidScript::class);
    }

    public function test_it_passes_any_registered_script()
    {
        Script::$registered->each(function ($script) {
            $this->assertTrue(
                $this->rule->passes('attribute', $script->class),
                "Script by class [{$script->class}] did not pass the ValidScript Rule."
            );

            $this->assertTrue(
                $this->rule->passes('attribute', $script->name),
                "Script by name [{$script->name}] did not pass the ValidScript Rule."
            );

            $humanizedName = Str::snake($script->name, ' ');
            $this->assertTrue(
                $this->rule->passes('attribute', $script->name),
                "Script by human name [{$humanizedName}] did not pass the ValidScript Rule."
            );
        });
    }

    public function test_it_fails_an_invalid_script()
    {
        $this->assertFalse(
            $this->rule->passes('attribute', 'InvalidScript')
        );

        $this->assertFalse(
            $this->rule->passes('attribute', 'Invalid Script')
        );
    }
}
