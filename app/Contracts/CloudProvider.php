<?php

namespace App\Contracts;

use Closure;
use GuzzleHttp\Client;
use App\ServerConfiguration;

interface CloudProvider
{
    /**
     * Get the HTTP Client
     *
     * @return Client
     */
    public function client();

    /**
     * Get all of the valid regions for the provider.
     *
     * @return array
     */
    public function regions();

    /**
     * Get all of the valid server sizes for the provider.
     *
     * @return array
     */
    public function sizes();

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
     * @throws \GuzzleHttp\Exception\ClientException
     * @return array
     */
    public function request($method, $uri, $data = []);

    /**
     * Deploy a server on the cloud provider.
     *
     * @param Closure|ServerConfiguration $configuration
     * @param array|string $sshKeys
     * @return string [The ID of the deployed server]
     */
    public function createServer($configuration, $sshKeys);

    /**
     * Delete a deployed server
     *
     * @param string|Deployable $id
     * @return void
     */
    public function deleteServer($id);
}
