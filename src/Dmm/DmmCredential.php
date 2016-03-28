<?php
namespace Dmm;

/**
 * @package Dmm
 */
class DmmCredential
{
    /**
     * @var string The affiliate ID.
     */
    protected $affiliateId;

    /**
     * @var string The API ID.
     */
    protected $apiId;

    /**
     * Constructor
     *
     * @param string $affiliateId
     * @param string $apiId
     */
    public function __construct($affiliateId, $apiId)
    {
        $this->affiliateId = $affiliateId;
        $this->apiId = $apiId;
    }

    /**
     * Returns the affiliate ID.
     *
     * @return string
     */
    public function getAffiliateId()
    {
        return $this->affiliateId;
    }

    /**
     * Returns the API ID.
     *
     * @return string
     */
    public function getApiId()
    {
        return $this->apiId;
    }

    /**
     * Sets the affiliate ID.
     *
     * @param string The affiliate ID.
     */
    public function setAffiliateId($affiliateId)
    {
        $this->affiliateId = $affiliateId;
    }

    /**
     * Sets the API ID.
     *
     * @param string The API ID.
     */
    public function setApiId($apiId)
    {
        $this->apiId = $apiId;
    }

    public function validateAffiliateId($affiliate_id = null)
    {
        if (empty($affiliate_id)) {
            $affiliate_id = $this->getAffiliateId();
        }

        if (is_string($affiliate_id) && preg_match('/^.+-99[0-9]$/', $affiliate_id)) {
            return TRUE;
        }
        return FALSE;
    }

    public function validateApiId($api_id = null)
    {
        if (empty($api_id)) {
            $api_id = $this->getApiId();
        }

        if (is_string($api_id) && $api_id !== "") {
            return TRUE;
        }
        return FALSE;
    }

    public function validateCredentials()
    {
        return ($this->validateAffiliateId() && $this->validateApiId());
    }
}
