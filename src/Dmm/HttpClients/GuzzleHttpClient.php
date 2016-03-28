<?php
namespace Dmm\HttpClients;

use Dmm\Http\RawResponse;
use Dmm\Exceptions\DmmSDKException;

use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Ring\Exception\RingException;
use GuzzleHttp\Exception\RequestException;

/**
 * @package Dmm
 */
class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * @var \GuzzleHttp\Client The Guzzle client.
     */
    protected $guzzleClient;

    /**
     * @param \GuzzleHttp\Client|null The Guzzle client.
     */
    public function __construct(Client $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient ?: new Client();
    }

    /**
     * @inheritdoc
     */
    public function send($url, $method, $body, array $headers, $timeOut)
    {
        $options = [
            'headers' => $headers,
            'body' => $body,
            'timeout' => $timeOut,
            'connect_timeout' => 10,
        ];
        $request = $this->guzzleClient->createRequest($method, $url, $options);

        try {
            $rawResponse = $this->guzzleClient->send($request);
        } catch (RequestException $e) {
            $rawResponse = $e->getResponse();

            if ($e->getPrevious() instanceof RingException || !$rawResponse instanceof ResponseInterface) {
                throw new DmmSDKException($e->getMessage(), $e->getCode());
            }
        }

        $rawHeaders = $this->getHeadersAsString($rawResponse);
        $rawBody = $rawResponse->getBody();
        $httpStatusCode = $rawResponse->getStatusCode();

        return new RawResponse($rawHeaders, $rawBody, $httpStatusCode);
    }

    /**
     * Returns the Guzzle array of headers as a string.
     *
     * @param ResponseInterface $response The Guzzle response.
     *
     * @return string
     */
    public function getHeadersAsString(ResponseInterface $response)
    {
        $headers = $response->getHeaders();
        $rawHeaders = [];
        foreach ($headers as $name => $values) {
            $rawHeaders[] = $name . ": " . implode(", ", $values);
        }

        return implode("\r\n", $rawHeaders);
    }
}
