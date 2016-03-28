<?php
namespace Dmm\Http;

/**
 * Class RequestBodyUrlEncoded
 *
 * @package Dmm
 */
class RequestBodyUrlEncoded
{
    /**
     * @var array The parameters to send with this request.
     */
    protected $params = [];

    /**
     * Creates a new UrlEncodedBody entity.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return http_build_query($this->params, null, '&');
    }
}
