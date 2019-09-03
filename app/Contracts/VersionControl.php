<?php

namespace App\Contracts;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

interface VersionControl
{
    /**
     * Get the HTTP Client
     *
     * @return Client
     */
    public function client();

    /**
     * Whether the cloud is properly authenticated with the provider API
     *
     * @return boolean
     */
    public function isAuthenticated();

    /**
     * Dispatch a HTTP Request to the providers API
     *
     * @param string $method
     * @param string $uri
     * @param array $data
     * @throws ClientException
     * @return array
     */
    public function request($method, $uri, $data = []);

    /**
     * Clone the given repository
     *
     * @param string $repository
     * @param string $hash
     * @return string
     */
    public function tarballUrl($repository, $hash = null);

    /**
     * Get the latest hash for the repository
     *
     * @param string $repository
     * @return string
     */
    public function latestHash($repository);
}
