<?php

namespace App;

use Closure;
use Illuminate\Support\Str;

class ServerConfiguration
{
    /**
     * The name of the server we deploy
     *
     * @var string
     */
    public $name = 'unique-snowflake-1234';

    /**
     * The resources to allocate for the server
     *
     * @var string
     */
    public $size;

    /**
     * the distro image to deploy
     *
     * @var string
     */
    public $image;

    /**
     * Where do we deploy the server
     *
     * @var string
     */
    public $region;

    /**
     * Whether to support IPv6
     *
     * @var boolean
     */
    public $ipv6;

    /**
     * Whether to support private networking
     *
     * @var boolean
     */
    public $privateNetworking;

    /**
     * Whether to support monitoring
     *
     * @var boolean
     */
    public $monitoring;

    /**
     * Make a new server configuration instance
     *
     * @param ServerConfiguration|Closure|array $configuration
     * @return ServerConfiguration
     */
    public static function make($configuration)
    {
        if ($configuration instanceof self) {
            return $configuration;
        }

        return new static($configuration);
    }

    /**
     * Create a new ServerConfiguration instance
     *
     * @param Closure|array $configuration
     */
    public function __construct($configuration)
    {
        if ($configuration instanceof Closure) {
            $configuration($this);
        } else {
            foreach (collect($configuration) as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Dynamically retrieve the value of an property.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        if (\property_exists($this, $property = Str::snake($key))) {
            return $this->$property;
        }

        return null;
    }

    /**
     * Dynamically check if an property is set.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return property_exists($this, $key);
    }
}
