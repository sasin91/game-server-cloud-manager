<?php

namespace App\CloudProviders;

use Closure;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use App\ServerConfiguration;
use App\Contracts\CloudProvider;
use function GuzzleHttp\json_decode;
use Illuminate\Support\Facades\Cache;

class DigitalOcean implements CloudProvider
{
    /**
     * HTTP Client configured for DigitalOcean API
     *
     * @var Client
     */
    protected $client;

    /**
     * Create a new instance
     *
     * @param string $apiToken
     */
    public function __construct($apiToken)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.digitalocean.com/v2/',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$apiToken}"
            ]
        ]);
    }

    /**
     * Get the HTTP Client
     *
     * @return Client
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * Get all of the valid regions for the provider.
     *
     * @return array
     */
    public function regions()
    {
        return Cache::remember('DigitalOcean:regions', 15, function () {
            return $this->request('GET', 'regions');
        });
    }

    /**
     * Get all of the valid server sizes for the provider.
     *
     * @return array
     */
    public function sizes()
    {
        return Cache::remember('DigitalOcean:sizes', 15, function () {
            return $this->request('GET', 'sizes');
        });
    }

    /**
     * Whether the cloud is properly authenticated with the provider API
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        try {
            $this->request('GET', '/droplets');

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Dispatch a HTTP Request to the providers API
     *
     * @param string $method
     * @param string $uri
     * @param array $data
     * @throws \GuzzleHttp\Exception\ClientException
     * @return array
     */
    public function request($method, $uri, $data = [])
    {
        $response = $this->client->request($method, $uri, $data);

        return json_decode((string)$response->getBody(), true);
    }

    /**
     * Deploy a server on the cloud provider.
     *
     * @param array|Closure|ServerConfiguration $configuration
     * @param array|string $sshKeys
     * @return string [The ID of the deployed server]
     */
    public function createServer($configuration, $sshKeys)
    {
        $configuration = ServerConfiguration::make($configuration);

        $data = $this->request('POST', '/droplets', [
            'name' => (string)$configuration->name,
            'size' => (string)($configuration->size ?? $this->sizes()[0]),
            'region' => (string)($configuration->region ?? $this->regions()[0]),
            'image' => (string)($configuration->image ?? 'ubuntu-18-04-x64'),
            'ipv6' => (boolean)$configuration->ipv6 ?? true,
            'private_networking' => (boolean)$configuration->privateNetworking ?? true,
            'monitoring' => (boolean)$configuration->monitoring ?? true,
            'ssh_keys' => Arr::wrap($sshKeys),
        ]);

        return $data['droplet']['id'];
    }

    /**
     * Delete a deployed server
     *
     * @param string $id
     * @return void
     */
    public function deleteServer($id)
    {
        $this->request('DELETE', "/droplets/{$id}");
    }
}
