<?php

namespace App;

use function is_null;
use function optional;
use ReflectionException;
use Illuminate\Support\Collection;
use App\Filesystem\FileIndex;
use App\Exceptions\VersionControlNotFound;
use Illuminate\Contracts\Foundation\Application;
use App\Contracts\VersionControl as VersionControlContract;

class VersionControl
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
        static::$registered = FileIndex::scan('VersionControl')
            ->each(function ($versionControl) use ($app) {
                $app->singleton($versionControl->class);
                $app->alias($versionControl->class, $versionControl->name);
            });

        return static::$registered;
    }

    /**
     * Create a new VersionControl instance
     *
     * @param string|null $driver
     * @throws VersionControlNotFound
     * @throws ReflectionException
     * @return VersionControlContract
     */
    public static function make($driver = null)
    {
        $driver = $driver ?? optional(static::$registered->first())->name;

        throw_if(is_null($driver), VersionControlNotFound::make($driver));

        return resolve($driver);
    }
}
