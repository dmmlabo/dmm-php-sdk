<?php
namespace Dmm\Tests\HttpClients;

use Mockery as m;
use Dmm\HttpClients\GuzzleHttpClient;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Exception\RequestException;

class GuzzleHttpClientTest extends AbstractTestHttpClient
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzleMock;

    /**
     * @var GuzzleHttpClient
     */
    protected $guzzleClient;

    public function setUp()
    {
        $this->guzzleMock = m::mock('GuzzleHttp\Client');
        $this->guzzleClient = new GuzzleHttpClient($this->guzzleMock);
    }

    public function testCanSendNormalRequest()
    {
        $request = new Request('GET', 'http://foo.com');

        $body = Stream::factory($this->fakeRawBody);
        $response = new Response(200, $this->fakeHeadersAsArray, $body);

        $this->guzzleMock
            ->shouldReceive('createRequest')
            ->once()
            ->with('GET', 'http://foo.com/', m::on(function ($arg) {

                // array_diff_assoc() will sometimes trigger error on child-arrays
                if (['X-foo' => 'bar'] !== $arg['headers']) {
                    return false;
                }
                unset($arg['headers']);

                return true;
            }))
            ->andReturn($request);
        $this->guzzleMock
            ->shouldReceive('send')
            ->once()
            ->with($request)
            ->andReturn($response);

        $response = $this->guzzleClient->send('http://foo.com/', 'GET', 'foo_body', ['X-foo' => 'bar'], 123);

        $this->assertInstanceOf('Dmm\Http\RawResponse', $response);
        $this->assertEquals($this->fakeRawBody, $response->getBody());
        $this->assertEquals($this->fakeHeadersAsArray, $response->getHeaders());
        $this->assertEquals(200, $response->getHttpResponseCode());
    }

    /**
     * @expectedException \Dmm\Exceptions\DmmSDKException
     */
    public function testThrowsExceptionOnClientError()
    {
        $request = new Request('GET', 'http://foo.com');

        $this->guzzleMock
            ->shouldReceive('createRequest')
            ->once()
            ->with('GET', 'http://foo.com/', m::on(function ($arg) {

                // array_diff_assoc() will sometimes trigger error on child-arrays
                if ([] !== $arg['headers']) {
                    return false;
                }
                unset($arg['headers']);

                return true;
            }))
            ->andReturn($request);
        $this->guzzleMock
            ->shouldReceive('send')
            ->once()
            ->with($request)
            ->andThrow(new RequestException('Foo', $request));

        $this->guzzleClient->send('http://foo.com/', 'GET', 'foo_body', [], 60);
    }
}
