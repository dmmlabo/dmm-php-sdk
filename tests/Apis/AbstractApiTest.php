<?php
namespace Dmm\Tests\Apis;

use Dmm\DmmCredential;
use Dmm\DmmClient;
use Dmm\Apis\AbstractApi;

class AbstractApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string The affiliate ID.
     */
    protected $affiliateId;

    /**
     * @var string The API ID.
     */
    protected $apiId;

    public function testCanInstantiate()
    {
        $client     = new DmmClient();
        $credential = new DmmCredential("test-999", "iiiiiiiid");

        $api = new AbstractApiTestInstance($client, $credential);

        $this->assertInstanceOf('Dmm\Apis\ApiInterface', $api);
        $this->assertInstanceOf('Dmm\DmmClient', $api->getClient());
        $this->assertEquals($client, $api->getClient());
        $this->assertInstanceOf('Dmm\DmmCredential', $api->getCredential());

        $apiCredential = $api->getCredential();
        $this->assertEquals("test-999", $apiCredential->getAffiliateId());
        $this->assertEquals("iiiiiiiid", $apiCredential->getApiId());
    }

    /**
     *  @expectedException \Dmm\Exceptions\DmmSDKException
     */
    public function testInstantiatingWithoutCredentialThrows()
    {
        $client = new DmmClient();

        $api = new AbstractApiTestInstance($client, null);
    }

    /**
     *  @expectedException \Dmm\Exceptions\DmmSDKException
     */
    public function testInstantiatingWithoutClientThrows()
    {
        $credential = new DmmCredential("test-999", "iiiiiiiid");

        $api = new AbstractApiTestInstance(null, $credential);
    }

    /**
     * @group integration
     */
    public function testCanGetRequest()
    {
        $this->initializeTestIds();

        $client     = new DmmClient();
        $credential = new DmmCredential($this->affiliateId, $this->apiId);
        $api = new AbstractApiTestInstance($client, $credential);
        $response = $api->get("/FloorList");

        $this->assertInstanceOf('Dmm\DmmResponse', $response);
    }

    /**
     * @group integration
     */
    public function testCanRequestApi()
    {
        $this->initializeTestIds();

        $client     = new DmmClient();
        $credential = new DmmCredential($this->affiliateId, $this->apiId);
        $api = new AbstractApiTestInstance($client, $credential);
        $response = $api->sendRequest("GET", "/ItemList", ["site" => "DMM.R18"]);

        $this->assertInstanceOf('Dmm\DmmResponse', $response);

        $lastResponse = $api->getLastResponse();
        $this->assertInstanceOf('Dmm\DmmResponse', $lastResponse);
        $this->assertEquals($response, $lastResponse);

    }

    public function initializeTestIds()
    {
        $affiliateId = getenv("DMM_TEST_AFFILIATE_ID");
        $apiId       = getenv("DMM_TEST_API_ID");

        if (empty($affiliateId) || empty($apiId)) {
            if (!file_exists(__DIR__ . '/DmmTestCredentials.php')) {
                throw new DmmSDKException(
                    'You must create a DmmTestCredentials.php file from DmmTestCredentials.php.dist'
                );
            }

            if (!strlen(DmmTestCredentials::$affiliateId) ||
                !strlen(DmmTestCredentials::$apiId)
            ) {
                throw new DmmSDKException(
                    'You must fill out DmmTestCredentials.php'
                );
            }
            $affiliateId = DmmTestCredentials::$affiliateId;
            $apiId       = DmmTestCredentials::$apiId;
        }

        $this->affiliateId = $affiliateId;
        $this->apiId       = $apiId;
    }
}

class AbstractApiTestInstance extends AbstractApi
{
}
