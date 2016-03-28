<?php
namespace Dmm;

use Dmm\Apis\ApiInterface;
use Dmm\HttpClients\HttpClientsFactory;
use Dmm\Exceptions\DmmSDKException;
use Dmm\Exceptions\InvalidArgumentException;

/**
 * Class Dmm
 *
 * @package Dmm
 */
class Dmm
{
    /**
     * @const string Version number of the Dmm PHP SDK.
     */
    const VERSION = '0.0.1';

    /**
     * @const string The name of the environment variable that contains the app secret.
     */
    const AFFILIATE_ID_ENV_NAME = 'DMM_AFFILIATE_ID';

    /**
     * @const string The name of the environment variable that contains the app ID.
     */
    const API_ID_ENV_NAME = 'DMM_API_ID';

    /**
     * @var DmmCredential The DmmCredential entity.
     */
    protected $credential;

    /**
     * @var DmmClient The Dmm client service.
     */
    protected $client;

    /**
     * @var DmmResponse|null Stores the last request made to API.
     */
    protected $lastResponse;

    /**
     * Instantiates a new Dmm super-class object.
     *
     * @param array $config
     *
     * @throws DmmSDKException
     */
    public function __construct(array $config = [])
    {
        $config = array_merge([
            'api_id'              => getenv(static::API_ID_ENV_NAME),
            'affiliate_id'        => getenv(static::AFFILIATE_ID_ENV_NAME),
            'http_client_handler' => null,
        ], $config);

        if (!$config['affiliate_id']) {
            throw new DmmSDKException('Required "affiliate_id" key not supplied in config and could not find fallback environment variable "' . static::AFFILIATE_ID_ENV_NAME . '"');
        }
        if (!$config['api_id']) {
            throw new DmmSDKException('Required "api_id" key not supplied in config and could not find fallback environment variable "' . static::API_ID_ENV_NAME . '"');
        }
        $this->credential = new DmmCredential($config['affiliate_id'], $config['api_id']);
        $this->client = new DmmClient(
            HttpClientsFactory::createHttpClient($config['http_client_handler'])
        );
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
     * Returns the DmmClient service.
     *
     * @return DmmClient
     */
    public function getClient()
    {
        return $this->client;
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
     * Sends a GET request to Graph and returns the result.
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

    /**
     * Get API interface
     *
     * @param  string $name
     *
     * @return ApiInterface
     *
     * @throws InvalidArgumentException
     */
    public function api($name)
    {
        switch ($name) {
            case 'actress':
                $api = new Apis\Actress($this->client, $this->credential);
                break;
            case 'author':
                $api = new Apis\Author($this->client, $this->credential);
                break;
            case 'floor':
                $api = new Apis\Floor($this->client, $this->credential);
                break;
            case 'genre':
                $api = new Apis\Genre($this->client, $this->credential);
                break;
            case 'maker':
                $api = new Apis\Maker($this->client, $this->credential);
                break;
            case 'product':
                $api = new Apis\Product($this->client, $this->credential);
                break;
            case 'series':
                $api = new Apis\Series($this->client, $this->credential);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Undefined api instance called: "%s"', $name));
                break;
        }
        return $api;
    }
}
