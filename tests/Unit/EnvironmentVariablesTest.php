<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\EnvironmentVariables;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EnvironmentVariablesTest extends TestCase
{
    public function test_it_parses_an_array_of_variables_into_a_string()
    {
        $variables = EnvironmentVariables::stringify([
            'AAA' => 'BBB',
            'BBB' => 'CCC'
        ]);

        $this->assertEquals(
            'AAA=BBB'.PHP_EOL.'BBB=CCC',
            $variables
        );
    }

    public function test_it_parses_a_string_of_variables_into_a_collection()
    {
        $variables = EnvironmentVariables::collect('AAA=BBB'.PHP_EOL.'CCC=BBB');

        $this->assertEquals([
            'AAA' => 'BBB',
            'CCC' => 'BBB'
        ], $variables->toArray());
    }
}
