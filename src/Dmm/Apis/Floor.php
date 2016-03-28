<?php
namespace Dmm\Apis;

/**
 * @package Dmm
 */
class Floor extends AbstractApi
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
    public function find()
    {
        return $this->get("/FloorList");
    }

}
