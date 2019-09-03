<?php

namespace App\CloudProviders;

use Closure;
use App\Cloud;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use App\ServerConfiguration;
use App\Contracts\Deployable;
use App\Contracts\CloudProvider as CloudProviderContract;

/**
 * Class CustomRestAPI
 * @package App\CloudProviders
 */
class CustomRestAPI implements CloudProviderContract
{
    /**
     * The cloud model
     *
     * @var Cloud
     */
    protected $cloud;

    /**
     * HTTP Client configured for Custom API
     *
     * @var Client
     */
    protected $client;

    /**
     * Create a new instance
     *
     * @param Cloud $cloud
     */
    public function __construct(Cloud $cloud)
    {
        $this->cloud = $cloud;

        $this->client = new Client([
            'base_uri' => $cloud->environment->variable('provider_url'),
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$cloud->environment->variable('provider_token')}"
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
        return [
            //
        ];
    }

    /**
     * Get all of the valid server sizes for the provider.
     *
     * @return array
     */
    public function sizes()
    {
        return [
            //
        ];
    }

    /**
     * Whether the cloud is properly authenticated with the provider API
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        try {
            $this->request('GET', '/');

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
     * @param Closure|ServerConfiguration $configuration
     * @param array|string $sshKeys
     * @return string [The ID of the deployed server]
     */
    public function createServer($configuration, $sshKeys)
    {
        $configuration = ServerConfiguration::make($configuration);

        $data = $this->request('POST', $this->cloud->environment->variable('provider_create_server_url', '/servers'), [
            'name' => (string)$configuration->name,
            'size' => (string)($configuration->size ?? Arr::first($this->sizes())),
            'region' => (string)($configuration->region ?? Arr::first($this->regions())),
            'image' => (string)($configuration->image ?? 'ubuntu-18-04-x64'),
            'ipv6' => (boolean)$configuration->ipv6 ?? true,
            'private_networking' => (boolean)$configuration->privateNetworking ?? true,
            'monitoring' => (boolean)$configuration->monitoring ?? true,
            'ssh_keys' => Arr::wrap($sshKeys),
        ]);

        return Arr::get($data, 'id');
    }

    /**
     * Delete a deployed server
     *
     * @param string|Deployable $id
     * @return void
     */
    public function deleteServer($id)
    {
        $key = ($id instanceof Deployable) ? $id->getProviderId() : (string)$id;

        $this->request(
            'DELETE',
            str_replace('//', '/', ($this->cloud->environment->variable('provider_delete_server_url', '/servers').'/'.$key))
        );
    }
}
