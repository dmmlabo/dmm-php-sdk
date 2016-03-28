<?php
namespace Dmm\HttpClients;

use Dmm\Http\RawResponse;
use Dmm\Exceptions\DmmSDKException;

/**
* @package Dmm
*/
class StreamHttpClient implements HttpClientInterface
{
    /**
     * @var DmmStream Procedural stream wrapper as object.
     */
    protected $dmmStream;

    /**
     * @param DmmStream|null Procedural stream wrapper as object.
     */
    public function __construct(DmmStream $dmmStream = null)
    {
        $this->dmmStream = $dmmStream ?: new DmmStream();
    }

    /**
     * @inheritdoc
     */
    public function send($url, $method, $body, array $headers, $timeOut)
    {
        $options = [
            'http' => [
                'method' => $method,
                'header' => $this->compileHeader($headers),
                'content' => $body,
                'timeout' => $timeOut,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => true, // All root certificates are self-signed
            ],
        ];

        $this->dmmStream->streamContextCreate($options);
        $rawBody = $this->dmmStream->fileGetContents($url);
        $rawHeaders = $this->dmmStream->getResponseHeaders();

        if ($rawBody === false || empty($rawHeaders)) {
            throw new DmmSDKException('Stream returned an empty response', 660);
        }

        $rawHeaders = implode("\r\n", $rawHeaders);

        return new RawResponse($rawHeaders, $rawBody);
    }

    /**
     * Formats the headers for use in the stream wrapper.
     *
     * @param array $headers The request headers.
     *
     * @return string
     */
    public function compileHeader(array $headers)
    {
        $header = [];
        foreach ($headers as $k => $v) {
            $header[] = $k . ': ' . $v;
        }

        return implode("\r\n", $header);
    }
}
