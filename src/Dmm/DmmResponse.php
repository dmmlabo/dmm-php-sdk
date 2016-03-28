<?php
namespace Dmm;

use Dmm\Exceptions\DmmResponseException;
use Dmm\Exceptions\DmmSDKException;

/**
 * Class DmmResponse
 *
 * @package Dmm
 */
class DmmResponse
{
    /**
     * @var int The HTTP status code response from Graph.
     */
    protected $httpStatusCode;

    /**
     * @var array The headers returned from Graph.
     */
    protected $headers;

    /**
     * @var string The raw body of the response from Graph.
     */
    protected $body;

    /**
     * @var array The decoded body of the Graph response.
     */
    protected $decodedBody = [];

    /**
     * @var DmmRequest The original request that returned this response.
     */
    protected $request;

    /**
     * @var DmmSDKException The exception thrown by this request.
     */
    protected $thrownException;

    /**
     * Creates a new Response entity.
     *
     * @param DmmRequest  $request
     * @param string|null $body
     * @param int|null    $httpStatusCode
     * @param array|null  $headers
     */
    public function __construct(DmmRequest $request, $body = null, $httpStatusCode = null, array $headers = [])
    {
        $this->request        = $request;
        $this->body           = $body;
        $this->httpStatusCode = $httpStatusCode;
        $this->headers        = $headers;

        $this->decodeBody();
    }

    /**
     * Return the original request that returned this response.
     *
     * @return DmmRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Return the DmmCredential entity used for this response.
     *
     * @return DmmCredential
     */
    public function getCredential()
    {
        return $this->request->getCredential();
    }

    /**
     * Return the HTTP status code for this response.
     *
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * Return the HTTP headers for this response.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Return the raw body response.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Return the decoded body response.
     *
     * @return array
     */
    public function getDecodedBody()
    {
        return $this->decodedBody;
    }

    /**
     * Returns true if API returned an error message.
     *
     * @return boolean
     */
    public function isError()
    {
        return (empty($this->httpStatusCode) || $this->httpStatusCode >= 400);
    }

    /**
     * Throws the exception.
     *
     * @throws DmmSDKException
     */
    public function throwException()
    {
        throw $this->thrownException;
    }

    /**
     * Instantiates an exception to be thrown later.
     */
    public function makeException()
    {
        $this->thrownException = DmmResponseException::create($this);
    }

    /**
     * Returns the exception that was thrown for this request.
     *
     * @return DmmSDKException|null
     */
    public function getThrownException()
    {
        return $this->thrownException;
    }

    /**
     * Convert the raw response into an array if possible.
     */
    public function decodeBody()
    {
        $this->decodedBody = json_decode($this->body, true);

        if ($this->decodedBody === null) {
            $this->decodedBody = [];
            parse_str($this->body, $this->decodedBody);
        }
        if (!is_array($this->decodedBody)) {
            $this->decodedBody = [];
        }

        if ($this->isError()) {
            $this->makeException();
        }
    }
}
