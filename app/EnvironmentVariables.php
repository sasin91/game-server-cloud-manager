<?php

namespace App;

use Dotenv\Lines;
use Dotenv\Parser;
use Illuminate\Support\Collection;

class EnvironmentVariables
{
    /**
     * Collect and parse given variables
     *
     * @param string $variables
     * @return Collection
     */
    public static function collect($variables)
    {
        return Collection::make(
            Lines::process(
                preg_split('/(\r\n|\n|\r)/', $variables)
            )
        )->flatMap(function ($line) {
            [$key, $value] = Parser::parse($line);

            return [$key => $value];
        });
    }

    /**
     * Stringify given variables
     *
     * @param array|Collection $variables
     * @return string
     */
    public static function stringify($variables)
    {
        return Collection::make($variables)->map(function ($value, $key) {
            return "{$key}={$value}";
        })->implode(PHP_EOL);
    }
}
