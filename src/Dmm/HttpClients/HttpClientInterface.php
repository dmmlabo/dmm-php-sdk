<?php
namespace Dmm\HttpClients;

/**
 * Interface HttpClientInterface
 *
 * @package Dmm
 */
interface HttpClientInterface
{
    /**
     * Sends a request to the server and returns the raw response.
     *
     * @param string $url     The endpoint to send the request to.
     * @param string $method  The request method.
     * @param string $body    The body of the request.
     * @param array  $headers The request headers.
     * @param int    $timeOut The timeout in seconds for the request.
     *
     * @return \Dmm\Http\RawResponse Raw response from the server.
     *
     * @throws \Dmm\Exceptions\DmmSDKException
     */
    public function send($url, $method, $body, array $headers, $timeOut);
}
