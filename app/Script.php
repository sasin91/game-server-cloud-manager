<?php

namespace App;

use function is_null;
use function optional;
use ReflectionException;
use Illuminate\Support\Collection;
use App\Filesystem\FileIndex;
use App\Exceptions\ScriptNotFound;
use Illuminate\Contracts\Foundation\Application;
use App\Contracts\Script as ScriptContract;
use Illuminate\Support\Str;

class Script
{
    /**
     * Collection of registered version control drivers
     *
     * @var Collection
     */
    public static $registered;

    /**
     * Register the discovered version control drivers with the given container
     *
     * @param Application $app
     * @return Collection
     */
    public static function register(Application $app)
    {
        return static::$registered = FileIndex::scan('Scripts')->each(function ($script) use ($app) {
            $app->bind($script->class);
            $app->alias($script->class, $script->name);
            $app->alias($script->class, Str::snake($script->name, ' '));
        });
    }

    /**
     * Create a new VersionControl instance
     *
     * @param string|null $driver
     * @param array $parameters
     * @throws ScriptNotFound
     * @throws ReflectionException
     * @return ScriptContract
     */
    public static function make($driver = null, $parameters = [])
    {
        $driver = $driver ?? optional(static::$registered->first())->name;

        throw_if(is_null($driver), ScriptNotFound::make($driver));

        return resolve($driver, $parameters);
    }

    /**
     * Check whether given script exists
     *
     * @param string $driver
     * @return boolean
     */
    public static function exists($driver)
    {
        if (app()->has($driver)) {
            return static::$registered->first(function ($script) use ($driver) {
                return $script->class === app()->getAlias($driver);
            }) !== null;
        }

        return false;
    }
}
