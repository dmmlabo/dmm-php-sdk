<?php
namespace Dmm\Apis;

/**
 * @package Dmm
 */
class Actress extends AbstractApi
{
    /**
     * Sends a request to Actress API and  returns the result.
     *
     * @param array $params
     *
     * @return DmmResponse
     *
     * @throws DmmSDKException
     */
    public function find(array $params = [])
    {
        return $this->get("/ActressSearch", $params);
    }
}
