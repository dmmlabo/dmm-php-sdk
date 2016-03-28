<?php
namespace Dmm\Apis;

use Dmm\Exceptions\DmmSDKException;

/**
 * @package Dmm
 */
class Author extends AbstractApi
{
    /**
     * Sends a request to Author API and  returns the result.
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
            throw new DmmSDKException('a correct floor id must be set when call Author API.');
        }

        return $this->get("/AuthorSearch", $params);
    }
}
