<?php
namespace Dmm\Exceptions;

use Dmm\DmmResponse;

/**
 * Class DmmResponseException
 *
 * @package Dmm
 */
class DmmResponseException extends DmmSDKException
{
    /**
     * @var DmmResponse The response that threw the exception.
     */
    protected $response;

    /**
     * @var array Decoded response.
     */
    protected $responseData;

    /**
     * Creates a DmmResponseException.
     *
     * @param DmmResponse     $response          The response that threw the exception.
     * @param DmmSDKException $previousException The more detailed exception.
     */
    public function __construct(DmmResponse $response, DmmSDKException $previousException = null)
    {
        $this->response = $response;
        $this->responseData = $response->getDecodedBody();

        $errorMessage = $this->get('message', 'Unknown error from API.');
        $errorCode = $this->get('status', -1);

        parent::__construct($errorMessage, $errorCode, $previousException);
    }

    /**
     * A factory for creating the appropriate exception based on the response from API.
     *
     * @param DmmResponse $response The response that threw the exception.
     *
     * @return DmmResponseException
     */
    public static function create(DmmResponse $response)
    {
        $data = $response->getDecodedBody();

        $code = isset($data['status']) ? $data['status'] : null;
        $message = isset($data['message']) ? $data['message'] : 'Unknown error from API.';

        // All others
        return new static($response, new DmmOtherException($message, $code));
    }

    /**
     * Checks isset and returns that or a default value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    private function get($key, $default = null)
    {
        if (isset($this->responseData[$key])) {
            return $this->responseData[$key];
        }

        return $default;
    }

    /**
     * Returns the HTTP status code
     *
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->response->getHttpStatusCode();
    }

    /**
     * Returns the error type
     *
     * @return string
     */
    public function getErrorType()
    {
        return $this->get('type', '');
    }

    /**
     * Returns the raw response used to create the exception.
     *
     * @return string
     */
    public function getRawResponse()
    {
        return $this->response->getBody();
    }

    /**
     * Returns the decoded response used to create the exception.
     *
     * @return array
     */
    public function getResponseData()
    {
        return $this->responseData;
    }

    /**
     * Returns the response entity used to create the exception.
     *
     * @return DmmResponse
     */
    public function getResponse()
    {
        return $this->response;
    }
}
