<?php
namespace Dmm;

use Dmm\HttpClients\HttpClientInterface;
use Dmm\HttpClients\GuzzleHttpClient;
use Dmm\HttpClients\CurlHttpClient;
use Dmm\HttpClients\StreamHttpClient;
use Dmm\Exceptions\DmmSDKException;

/**
 * Class DmmClient
 *
 * @package Dmm
 */
class DmmClient
{
    /**
     * @const string Production API URL.
     */
    const BASE_API_URL = "https://api.dmm.com/affiliate/v3";

    /**
     * @const integer The timeout in seconds for a normal request.
     */
    const DEFAULT_REQUEST_TIMEOUT = 60;

    /**
     * @var HttpClientInterface HTTP client handler.
     */
    protected $httpClientHandler;

    /**
     * @var integer The number of calls that have been made to Graph.
     */
    public static $requestCount = 0;

    /**
     * Instantiates a new DmmClient object.
     *
     * @param HttpClientInterface|null $httpClientHandler
     */
    public function __construct(HttpClientInterface $httpClientHandler = null)
    {
        $this->httpClientHandler = $httpClientHandler ?: $this->detectHttpClientHandler();
    }

    /**
     * Sets the HTTP client handler.
     *
     * @param HttpClientInterface $httpClientHandler
     */
    public function setHttpClientHandler(HttpClientInterface $httpClientHandler)
    {
        $this->httpClientHandler = $httpClientHandler;
    }

    /**
     * Returns the HTTP client handler.
     *
     * @return HttpClientInterface
     */
    public function getHttpClientHandler()
    {
        return $this->httpClientHandler;
    }

    /**
     * Detects which HTTP client handler to use.
     *
     * @return HttpClientInterface
     */
    public function detectHttpClientHandler()
    {
        $handler = null;
        if (class_exists('GuzzleHttp\Client')) {
            $handler = new GuzzleHttpClient();
        } elseif (function_exists('curl_init')) {
            $handler = new CurlHttpClient();
        } else {
            $handler = new StreamHttpClient();
        }
        return $handler;
    }

    /**
     * Returns the base API URL.
     *
     * @return string
     */
    public function getBaseApiUrl()
    {
        return static::BASE_API_URL;
    }

    /**
     * Prepares the request for sending to the client handler.
     *
     * @param DmmRequest $request
     *
     * @return array
     */
    public function prepareRequestMessage(DmmRequest $request)
    {
        $url = $this->getBaseApiUrl() . $request->getUrl();
        $requestBody = $request->getUrlEncodedBody();
        if ($request->getMethod() == "POST") {
            $request->setHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]);
        }

        return [
            $url,
            $request->getMethod(),
            $request->getHeaders(),
            $requestBody->getBody(),
        ];
    }

    /**
     * Makes the request to API and returns the result.
     *
     * @param DmmRequest $request
     *
     * @return DmmResponse
     *
     * @throws DmmSDKException
     */
    public function sendRequest(DmmRequest $request)
    {
        if (get_class($request) === 'Dmm\DmmRequest') {
            $request->validateCredentials();
        }

        list($url, $method, $headers, $body) = $this->prepareRequestMessage($request);

        // Since file uploads can take a while, we need to give more time for uploads
        $timeOut = static::DEFAULT_REQUEST_TIMEOUT;

        // Should throw `DmmSDKException` exception on HTTP client error.
        // Don't catch to allow it to bubble up.
        $rawResponse = $this->httpClientHandler->send($url, $method, $body, $headers, $timeOut);

        static::$requestCount++;

        $returnResponse = new DmmResponse(
            $request,
            $rawResponse->getBody(),
            $rawResponse->getHttpResponseCode(),
            $rawResponse->getHeaders()
        );

        if ($returnResponse->isError()) {
            throw $returnResponse->getThrownException();
        }

        return $returnResponse;
    }
}
