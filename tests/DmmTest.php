<?php
namespace Dmm\Tests;

use Dmm\Dmm;
use Dmm\Apis;
use Dmm\DmmClient;
use Dmm\Http\RawResponse;
use Dmm\HttpClients\HttpClientInterface;
use Dmm\DmmRequest;

class FooClientInterface implements HttpClientInterface
{
    public function send($url, $method, $body, array $headers, $timeOut)
    {
        return new RawResponse(
            "HTTP/1.1 1337 OK\r\nDate: Mon, 19 May 2014 18:37:17 GMT",
            '{"data":[{"id":"123","name":"Foo"},{"id":"1337","name":"Bar"}]}'
        );
    }
}

class DmmTest extends \PHPUnit_Framework_TestCase
{
    const COMMON_NAMESPACE = 'Dmm\Apis\\';
    const COMMON_INTERFACE = 'Dmm\Apis\ApiInterface';

    /**
     * @var DmmCredential
     */
    public static $testDmmCredential;

    /**
     * @var DmmClient
     */
    public static $testDmmClient;

    protected $config = [
        'affiliate_id' => 'test-990',
        'api_id'       => 'foo_id',
    ];

    /**
     * @expectedException \Dmm\Exceptions\DmmSDKException
     */
    public function testInstantiatingWithoutAffiliateIdThrows()
    {
        // unset value so there is no fallback to test expected Exception
        putenv(Dmm::AFFILIATE_ID_ENV_NAME.'=');
        $config = [
            'api_id' => 'foo_id',
        ];
        $dmm = new Dmm($config);
    }

    /**
     * @expectedException \Dmm\Exceptions\DmmSDKException
     */
    public function testInstantiatingWithoutApiIdThrows()
    {
        // unset value so there is no fallback to test expected Exception
        putenv(Dmm::API_ID_ENV_NAME.'=');
        $config = [
            'affiliate_id' => 'test-990',
        ];
        $dmm = new Dmm($config);
    }

    /**
     * @expectedException \Dmm\Exceptions\InvalidArgumentException
     */
    public function testSettingAnInvalidHttpClientHandlerThrows()
    {
        $config = array_merge($this->config, [
            'http_client_handler' => 'foo_handler',
        ]);
        $dmm = new Dmm($config);
    }

    public function testCurlHttpClientHandlerCanBeForced()
    {
        $config = array_merge($this->config, [
            'http_client_handler' => 'curl'
        ]);
        $dmm = new Dmm($config);
        $this->assertInstanceOf(
            'Dmm\HttpClients\CurlHttpClient',
            $dmm->getClient()->getHttpClientHandler()
        );
    }

    public function testStreamHttpClientHandlerCanBeForced()
    {
        $config = array_merge($this->config, [
            'http_client_handler' => 'stream'
        ]);
        $dmm = new Dmm($config);
        $this->assertInstanceOf(
            'Dmm\HttpClients\StreamHttpClient',
            $dmm->getClient()->getHttpClientHandler()
        );
    }

    public function testGuzzleHttpClientHandlerCanBeForced()
    {
        $config = array_merge($this->config, [
            'http_client_handler' => 'guzzle'
        ]);
        $dmm = new Dmm($config);
        $this->assertInstanceOf(
            'Dmm\HttpClients\GuzzleHttpClient',
            $dmm->getClient()->getHttpClientHandler()
        );
    }

    /**
     * @param mixed  $name
     * @param string $expected
     *
     * @dataProvider apiNamesProvider
     */
    public function testCreateHttpClient($name, $expected)
    {
        $dmm = new Dmm($this->config);
        $api = $dmm->api($name);

        $this->assertInstanceOf(self::COMMON_INTERFACE, $api);
        $this->assertInstanceOf($expected, $api);
    }

    /**
     * @return array
     */
    public function apiNamesProvider()
    {
        return [
            ['actress', self::COMMON_NAMESPACE . 'Actress'],
            ['author', self::COMMON_NAMESPACE . 'Author'],
            ['floor', self::COMMON_NAMESPACE . 'Floor'],
            ['genre', self::COMMON_NAMESPACE . 'Genre'],
            ['maker', self::COMMON_NAMESPACE . 'Maker'],
            ['product', self::COMMON_NAMESPACE . 'Product'],
            ['series', self::COMMON_NAMESPACE . 'Series'],
        ];
    }
}
