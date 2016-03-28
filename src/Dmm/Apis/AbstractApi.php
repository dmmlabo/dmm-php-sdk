<?php
namespace Dmm\Apis;

use Dmm\DmmClient;
use Dmm\DmmRequest;
use Dmm\DmmResponse;
use Dmm\DmmCredential;
use Dmm\Exceptions\DmmSDKException;

/**
 * @package Dmm
 */
abstract class AbstractApi implements ApiInterface
{
    /**
     * @var DmmResponse|null Stores the last request made to API.
     */
    protected $lastResponse;

    /**
     * @var DmmClient The Dmm client service.
     */
    protected $client;

    /**
     * @var DmmCredential The DmmCredential entity.
     */
    protected $credential;

    /**
     * Contructor
     *
     * @param DmmClient     $client     The Dmm client service.
     * @param DmmCredential $credential The DmmCredential entity.
     */
    public function __construct(DmmClient $client = null, DmmCredential $credential = null)
    {
        if (is_null($client)) {
            throw new DmmSDKException('Client must be set when call APIs.');
        }

        if (is_null($credential)) {
            throw new DmmSDKException('Credentials must be set when call APIs.');
        }

        $this->client     = $client;
        $this->credential = $credential;
    }

    /**
     * Returns the DmmClient service.
     *
     * @return DmmClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns the DmmCredential entity.
     *
     * @return DmmCredential
     */
    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * Sends a GET request to API and returns the result.
     *
     * @param string $endpoint
     * @param array  $params
     *
     * @return DmmResponse
     *
     * @throws DmmSDKException
     */
    public function get($endpoint, array $params = [])
    {
        return $this->sendRequest(
            'GET',
            $endpoint,
            $params
        );
    }

    /**
     * Returns the last response returned from API.
     *
     * @return DmmResponse|null
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Sends a request to Graph and returns the result.
     *
     * @param string                  $method
     * @param string                  $endpoint
     * @param array                   $params
     *
     * @return DmmResponse
     *
     * @throws DmmSDKException
     */
    public function sendRequest($method, $endpoint, array $params = [])
    {
        $request = $this->request($method, $endpoint, $params);

        return $this->lastResponse = $this->client->sendRequest($request);
    }

    /**
     * Instantiates a new DmmRequest entity.
     *
     * @param string $method
     * @param string $endpoint
     * @param array  $params
     *
     * @return DmmRequest
     *
     * @throws DmmSDKException
     */
    public function request($method, $endpoint, array $params = [])
    {
        return new DmmRequest(
            $this->credential,
            $method,
            $endpoint,
            $params
        );
    }
}