<?php
namespace Dmm\HttpClients;

use Dmm\Exceptions\InvalidArgumentException;
use Dmm\Exceptions\Exception;

use GuzzleHttp\Client;

/**
 * @package Dmm
 */
class HttpClientsFactory
{
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    /**
     * HTTP client generation.
     *
     * @param DmmHttpClientInterface|Client|string|null $handler
     *
     * @throws Exception                If the cURL extension or the Guzzle client aren't available (if required).
     * @throws InvalidArgumentException If the http client handler isn't "curl", "stream", "guzzle", or an instance of Dmm\HttpClients\HttpClientInterface.
     *
     * @return HttpClientInterface
     */
    public static function createHttpClient($handler)
    {
        if (!$handler) {
            return self::detectDefaultClient();
        }

        if ($handler instanceof HttpClientInterface) {
            return $handler;
        }

        if ('stream' === $handler) {
            return new StreamHttpClient();
        }
        if ('curl' === $handler) {
            if (!extension_loaded('curl')) {
                throw new Exception('The cURL extension must be loaded in order to use the "curl" handler.');
            }

            return new CurlHttpClient();
        }

        if ('guzzle' === $handler && !class_exists('GuzzleHttp\Client')) {
            throw new Exception('The Guzzle HTTP client must be included in order to use the "guzzle" handler.');
        }

        if ($handler instanceof Client) {
            return new GuzzleHttpClient($handler);
        }
        if ('guzzle' === $handler) {
            return new GuzzleHttpClient();
        }

        throw new InvalidArgumentException('The http client handler must be set to "curl", "stream", "guzzle", be an instance of GuzzleHttp\Client or an instance of Dmm\HttpClients\HttpClientInterface');
    }

    /**
     * Detect default HTTP client.
     *
     * @return HttpClientInterface
     */
    private static function detectDefaultClient()
    {
        if (class_exists('GuzzleHttp\Client')) {
            return new GuzzleHttpClient();
        }

        if (extension_loaded('curl')) {
            return new CurlHttpClient();
        }

        return new StreamHttpClient();
    }
}
