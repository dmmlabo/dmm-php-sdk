<?php
namespace Dmm;

use Dmm\Url\DmmUrlManipulator;
use Dmm\Http\RequestBodyUrlEncoded;
use Dmm\Exceptions\DmmSDKException;

/**
 * Class Request
 *
 * @package Dmm
 */
class DmmRequest
{
    /**
     * @var DmmCredential The Dmm credential entity.
     */
    protected $credential;

    /**
     * @var string The HTTP method for this request.
     */
    protected $method;

    /**
     * @var string The Graph endpoint for this request.
     */
    protected $endpoint;

    /**
     * @var array The headers to send with this request.
     */
    protected $headers = [];

    /**
     * @var array The parameters to send with this request.
     */
    protected $params = [];

    /**
     * Creates a new Request entity.
     *
     * @param DmmCredential|null $credential
     * @param string|null        $method
     * @param string|null        $endpoint
     * @param array|null         $params
     */
    public function __construct(DmmCredential $credential = null, $method = null, $endpoint = null, array $params = [])
    {
        $this->setCredential($credential);
        $this->setMethod($method);
        $this->setEndpoint($endpoint);
        $this->setParams($params);
    }

    /**
     * Set the DmmCredential entity used for this request.
     *
     * @param DmmCredential|null $credential
     */
    public function setCredential(DmmCredential $credential = null)
    {
        $this->credential = $credential;
    }

    /**
     * Return the DmmCredential entity used for this request.
     *
     * @return DmmCredential
     */
    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * Validate that an access token exists for this request.
     *
     * @throws DmmSDKException
     */
    public function validateCredentials()
    {
        $credential = $this->credential->validateCredentials();
        if (!$credential) {
            throw new DmmSDKException('You must provide correct credentials.');
        }
    }

    /**
     * Set the HTTP method for this request.
     *
     * @param string
     *
     * @return DmmRequest
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
    }

    /**
     * Return the HTTP method for this request.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Validate that the HTTP method is set.
     *
     * @throws DmmSDKException
     */
    public function validateMethod()
    {
        if (!$this->method) {
            throw new DmmSDKException('HTTP method not specified.');
        }

        if (!in_array($this->method, ['GET', 'POST', 'DELETE'])) {
            throw new DmmSDKException('Invalid HTTP method specified.');
        }
    }

    /**
     * Set the endpoint for this request.
     *
     * @param string
     *
     * @return DmmRequest
     *
     * @throws DmmSDKException
     */
    public function setEndpoint($endpoint)
    {
        // Clean the credential information from the endpoint.
        $filterParams = ['affiliate_id', 'api_id'];
        $this->endpoint = DmmUrlManipulator::removeParamsFromUrl($endpoint, $filterParams);

        return $this;
    }

    /**
     * Return the HTTP method for this request.
     *
     * @return string
     */
    public function getEndpoint()
    {
        // For batch requests, this will be empty
        return $this->endpoint;
    }

    /**
     * Generate and return the headers for this request.
     *
     * @return array
     */
    public function getHeaders()
    {
        $headers = static::getDefaultHeaders();

        return array_merge($this->headers, $headers);
    }

    /**
     * Set the headers for this request.
     *
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Set the params for this request.
     *
     * @param array $params
     *
     * @return DmmRequest
     *
     * @throws DmmSDKException
     */
    public function setParams(array $params = [])
    {
        $this->dangerouslySetParams($params);

        return $this;
    }

    /**
     * Set the params for this request without filtering them first.
     *
     * @param array $params
     *
     * @return DmmRequest
     */
    public function dangerouslySetParams(array $params = [])
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    /**
     * Returns the body of the request as URL-encoded.
     *
     * @return RequestBodyUrlEncoded
     */
    public function getUrlEncodedBody()
    {
        $params = $this->getPostParams();

        return new RequestBodyUrlEncoded($params);
    }

    /**
     * Generate and return the params for this request.
     *
     * @return array
     */
    public function getParams()
    {
        $params = $this->params;

        $credential = $this->getCredential();
        if ($credential) {
            $params['affiliate_id'] = $credential->getAffiliateId();
            $params['api_id']       = $credential->getApiId();
        }

        return $params;
    }

    /**
     * Only return params on POST requests.
     *
     * @return array
     */
    public function getPostParams()
    {
        if ($this->getMethod() === 'POST') {
            return $this->getParams();
        }

        return [];
    }

    /**
     * Generate and return the URL for this request.
     *
     * @return string
     */
    public function getUrl()
    {
        $this->validateMethod();

        $endpoint = DmmUrlManipulator::forceSlashPrefix($this->getEndpoint());

        $url = $endpoint;

        if ($this->getMethod() !== 'POST') {
            $params = $this->getParams();
            $url = DmmUrlManipulator::appendParamsToUrl($url, $params);
        }

        return $url;
    }

    /**
     * Return the default headers that every request should use.
     *
     * @return array
     */
    public static function getDefaultHeaders()
    {
        return [
            'User-Agent' => 'dmm-php-sdk-' . Dmm::VERSION,
            'Accept-Encoding' => '*',
        ];
    }
}
