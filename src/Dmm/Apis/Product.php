<?php
namespace Dmm\Apis;

use Dmm\Exceptions\DmmSDKException;

/**
 * @package Dmm
 */
class Product extends AbstractApi
{
    const SITE_GENERAL = "DMM.com";
    const SITE_ADULT   = "FANZA";

    /**
     * Sends a request to Product API (DMM.com) and  returns the result.
     *
     * @param string $site
     * @param array  $params
     *
     * @return DmmResponse
     *
     * @throws DmmSDKException
     */
    public function find($site, array $params = [])
    {
        if (!in_array($site, $this->getSites())) {
            throw new DmmSDKException('site parameter must be set when call Product API.');
        }

        $params["site"] = $site;
        return $this->get("/ItemList", $params);
    }

    /**
     * Sends a request to Product API (DMM.com) and  returns the result.
     *
     * @param array $params
     *
     * @return DmmResponse
     *
     * @throws DmmSDKException
     */
    public function findGeneral(array $params = [])
    {
        return $this->find(static::SITE_GENERAL, $params);
    }

    /**
     * Sends a request to Product API (FANZA) and  returns the result.
     *
     * @param array $params
     *
     * @return DmmResponse
     *
     * @throws DmmSDKException
     */
    public function findAdult(array $params = [])
    {
        return $this->find(static::SITE_ADULT, $params);
    }

    /**
     * Gets DMM site types
     * @return array site types
     */
    protected function getSites()
    {
        return [static::SITE_GENERAL, static::SITE_ADULT];
    }
}
