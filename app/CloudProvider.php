<?php

namespace App;

use ReflectionException;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Filesystem\FileIndex;
use Symfony\Component\Finder\Finder;
use App\Exceptions\CloudProviderNotFound;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use App\Contracts\CloudProvider as CloudProviderContract;

/**
 * This class scans the app/CloudProviders directory and registers each with the container
 *
 * It also offers a convenient make method for instanciating registered provider classes.
 */
class CloudProvider
{
    /**
     * The registered providers
     *
     * @var Collection
     */
    public static $registered;

    /**
     * Registere the registered providers with given Container.
     *
     * @param Application $app
     * @return Collection
     */
    public static function register(Application $app)
    {
        static::$registered = FileIndex::scan('CloudProviders')->each(function ($cloudProvider) use ($app) {
            $app->singleton($cloudProvider->class);
            $app->alias($cloudProvider->class, $cloudProvider->name);
        });

        return static::$registered;
    }

    /**
     * Resolve a CloudProvider instance for given Cloud
     *
     * @param Cloud $cloud
     * @throws CloudProviderNotFound
     * @return CloudProviderContract
     */
    public static function make($cloud)
    {
        $provider = class_exists($cloud->provider)
                ? $cloud->provider
                : Str::studly($cloud->provider);

        try {
            return resolve($provider, [
                'apiToken' => $cloud->environment->variable('provider_token'),
                'cloud' => $cloud
            ]);
        } catch (ReflectionException $e) {
            throw CloudProviderNotFound::make($provider);
        }
    }
}
