<?php
namespace Dmm\Tests\HttpClients;

use Dmm\HttpClients\CurlHttpClient;
use Dmm\HttpClients\GuzzleHttpClient;
use Dmm\HttpClients\StreamHttpClient;
use Dmm\HttpClients\HttpClientsFactory;
use GuzzleHttp\Client;
use PHPUnit_Framework_TestCase;

class HttpClientsFactoryTest extends PHPUnit_Framework_TestCase
{
    const COMMON_NAMESPACE = 'Dmm\HttpClients\\';
    const COMMON_INTERFACE = 'Dmm\HttpClients\HttpClientInterface';

    /**
     * @param mixed  $handler
     * @param string $expected
     *
     * @dataProvider httpClientsProvider
     */
    public function testCreateHttpClient($handler, $expected)
    {
        $httpClient = HttpClientsFactory::createHttpClient($handler);

        $this->assertInstanceOf(self::COMMON_INTERFACE, $httpClient);
        $this->assertInstanceOf($expected, $httpClient);
    }

    /**
     * @return array
     */
    public function httpClientsProvider()
    {
        return [
            ['curl', self::COMMON_NAMESPACE . 'CurlHttpClient'],
            ['guzzle', self::COMMON_NAMESPACE . 'GuzzleHttpClient'],
            ['stream', self::COMMON_NAMESPACE . 'StreamHttpClient'],
            [new Client(), self::COMMON_NAMESPACE . 'GuzzleHttpClient'],
            [new CurlHttpClient(), self::COMMON_NAMESPACE . 'CurlHttpClient'],
            [new GuzzleHttpClient(), self::COMMON_NAMESPACE . 'GuzzleHttpClient'],
            [new StreamHttpClient(), self::COMMON_NAMESPACE . 'StreamHttpClient'],
            [null, self::COMMON_INTERFACE],
        ];
    }
}
