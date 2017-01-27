<?php
namespace Dmm\Apis;

use Dmm\Exceptions\DmmSDKException;

/**
 * @package Dmm
 */
class Series extends AbstractApi
{
    /**
     * Sends a request to Series API and  returns the result.
     *
     * @param integer|string $floor_id
     * @param array  $params
     *
     * @return DmmResponse
     *
     * @throws DmmSDKException
     */
    public function find($floor_id, array $params = [])
    {
        if (!is_integer($floor_id) && !is_numeric($floor_id)) {
            throw new DmmSDKException('a correct floor id must be set when call Series API.');
        }

        $params['floor_id'] = $floor_id;

        return $this->get("/SeriesSearch", $params);
    }
}
