<?php
namespace Dmm\Tests\HttpClients;

use Mockery as m;
use Dmm\HttpClients\StreamHttpClient;

class StreamHttpClientTest extends AbstractTestHttpClient
{
    /**
     * @var \Dmm\HttpClients\DmmStream
     */
    protected $streamMock;

    /**
     * @var StreamHttpClient
     */
    protected $streamClient;

    public function setUp()
    {
        $this->streamMock = m::mock('Dmm\HttpClients\DmmStream');
        $this->streamClient = new StreamHttpClient($this->streamMock);
    }

    public function testCanCompileHeader()
    {
        $headers = [
            'X-foo' => 'bar',
            'X-bar' => 'faz',
        ];
        $header = $this->streamClient->compileHeader($headers);
        $this->assertEquals("X-foo: bar\r\nX-bar: faz", $header);
    }

    public function testCanSendNormalRequest()
    {
        $this->streamMock
            ->shouldReceive('streamContextCreate')
            ->once()
            ->with(m::on(function ($arg) {
                if (!isset($arg['http']) || !isset($arg['ssl'])) {
                    return false;
                }

                if ($arg['http'] !== [
                        'method' => 'GET',
                        'header' => 'X-foo: bar',
                        'content' => 'foo_body',
                        'timeout' => 123,
                        'ignore_errors' => true,
                    ]
                ) {
                    return false;
                }

                return true;
            }))
            ->andReturn(null);
        $this->streamMock
            ->shouldReceive('getResponseHeaders')
            ->once()
            ->andReturn(explode("\n", trim($this->fakeRawHeader)));
        $this->streamMock
            ->shouldReceive('fileGetContents')
            ->once()
            ->with('http://foo.com/')
            ->andReturn($this->fakeRawBody);

        $response = $this->streamClient->send('http://foo.com/', 'GET', 'foo_body', ['X-foo' => 'bar'], 123);

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
        $this->streamMock
            ->shouldReceive('streamContextCreate')
            ->once()
            ->andReturn(null);
        $this->streamMock
            ->shouldReceive('getResponseHeaders')
            ->once()
            ->andReturn(null);
        $this->streamMock
            ->shouldReceive('fileGetContents')
            ->once()
            ->with('http://foo.com/')
            ->andReturn(false);

        $this->streamClient->send('http://foo.com/', 'GET', 'foo_body', [], 60);
    }
}
