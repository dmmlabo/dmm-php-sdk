<?php
namespace Dmm\Tests;

use Dmm\Exceptions\DmmSDKException;
use Dmm\Dmm;
use Dmm\DmmCredential;
use Dmm\DmmRequest;
use Dmm\DmmClient;
use Dmm\Http\RawResponse;
use Dmm\HttpClients\HttpClientInterface;
// These are needed when you uncomment the HTTP clients below.
use Dmm\HttpClients\CurlHttpClient;
use Dmm\HttpClients\GuzzleHttpClient;
use Dmm\HttpClients\StreamHttpClient;

class MyFooClientHandler implements HttpClientInterface
{
    public function send($url, $method, $body, array $headers, $timeOut)
    {
        return new RawResponse(
            "HTTP/1.1 200 OK\r\nDate: Mon, 19 May 2014 18:37:17 GMT",
            '{"data":[{"id":"123","name":"Foo"},{"id":"1337","name":"Bar"}]}'
        );
    }
}

class DmmClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DmmCredential
     */
    public $dmmCredential;

    /**
     * @var DmmClient
     */
    public $dmmClient;

    /**
     * @var DmmCredential
     */
    public static $testDmmCredential;

    /**
     * @var DmmClient
     */
    public static $testDmmClient;

    public function setUp()
    {
        $this->dmmCredential = new DmmCredential('test-999', 'wow');
        $this->dmmClient = new DmmClient(new MyFooClientHandler());
    }

    public function testACustomHttpClientCanBeInjected()
    {
        $handler = new MyFooClientHandler();
        $client = new DmmClient($handler);
        $httpHandler = $client->getHttpClientHandler();

        $this->assertInstanceOf('Dmm\Tests\MyFooClientHandler', $httpHandler);
    }

    public function testTheHttpClientWillFallbackToDefault()
    {
        $client = new DmmClient();
        $httpHandler = $client->getHttpClientHandler();

        if (class_exists('GuzzleHttp\Client')) {
            $this->assertInstanceOf('Dmm\HttpClients\GuzzleHttpClient', $httpHandler);
        } elseif (function_exists('curl_init')) {
            $this->assertInstanceOf('Dmm\HttpClients\CurlHttpClient', $httpHandler);
        } else {
            $this->assertInstanceOf('Dmm\HttpClients\StreamHttpClient', $httpHandler);
        }
    }

    public function testGetBaseApiUrl()
    {
        $client = new DmmClient();
        $url = $client->getBaseApiUrl();
        $this->assertEquals(DmmClient::BASE_API_URL, $url);
    }

    public function testADmmRequestEntityCanBeUsedToSendARequestToApi()
    {
        $dmmRequest = new DmmRequest($this->dmmCredential, 'GET', '/foo');
        $response = $this->dmmClient->sendRequest($dmmRequest);

        $this->assertInstanceOf('Dmm\DmmResponse', $response);
        $this->assertEquals(200, $response->getHttpStatusCode());
        $this->assertEquals('{"data":[{"id":"123","name":"Foo"},{"id":"1337","name":"Bar"}]}', $response->getBody());
    }

    /**
     * @group integration
     */
    public function testCanRequestApi()
    {
        $this->initializeTestCredential();

        // Get products
        $request = new DmmRequest(
            static::$testDmmCredential,
            'GET',
            '/ItemList',
            ["site" => "FANZA"]
        );
        $result = static::$testDmmClient->sendRequest($request)->getDecodedBody();

        $this->assertNotEmpty($result);
    }

    /**
     * @group integration
     * @expectedException \Dmm\Exceptions\DmmResponseException
     */
    public function testUncorrectRequestApi()
    {
        $request = new DmmRequest(
            static::$testDmmCredential,
            'GET',
            '/Foobar'
        );
        static::$testDmmClient->sendRequest($request);
    }

    public function initializeTestCredential()
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
            static::$testDmmCredential = new DmmCredential(
                DmmTestCredentials::$affiliateId,
                DmmTestCredentials::$apiId
            );
        } else {
            static::$testDmmCredential = new DmmCredential(
                $affiliateId,
                $apiId
            );
        }

        // Use default client
        $client = null;

        // Uncomment to enable curl implementation.
        //$client = new CurlHttpClient();

        // Uncomment to enable stream wrapper implementation.
        //$client = new StreamHttpClient();

        // Uncomment to enable Guzzle implementation.
        //$client = new GuzzleHttpClient();

        static::$testDmmClient = new DmmClient($client);
    }
}
