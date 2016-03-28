<?php
namespace Dmm\Tests;

use Dmm\DmmCredential;
use Dmm\DmmRequest;
use Dmm\DmmResponse;

class DmmResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Dmm\DmmRequest
     */
    protected $request;

    public function setUp()
    {
        $credential = new DmmCredential('123', 'foo_id');
        $this->request = new DmmRequest(
            $credential,
            'GET',
            '/ItemList?keyword=a',
            ['foo' => 'bar']
        );
    }

    public function testASuccessfulJsonResponseWillBeDecoded()
    {
        $graphResponseJson = '{"id":"123","name":"Foo"}';
        $response = new DmmResponse($this->request, $graphResponseJson, 200);

        $decodedResponse = $response->getDecodedBody();

        $this->assertFalse($response->isError(), 'Did not expect Response to return an error.');
        $this->assertEquals([
            'id' => '123',
            'name' => 'Foo',
        ], $decodedResponse);
    }

    public function testASuccessfulUrlEncodedKeyValuePairResponseWillBeDecoded()
    {
        $graphResponseKeyValuePairs = 'id=123&name=Foo';
        $response = new DmmResponse($this->request, $graphResponseKeyValuePairs, 200);

        $decodedResponse = $response->getDecodedBody();

        $this->assertFalse($response->isError(), 'Did not expect Response to return an error.');
        $this->assertEquals([
            'id' => '123',
            'name' => 'Foo',
        ], $decodedResponse);
    }

    public function testErrorStatusCanBeCheckedWhenAnErrorResponseIsReturned()
    {
        $graphResponse = '{"error":{"message":"Foo error.","type":"OAuthException","code":190,"error_subcode":463}}';
        $response = new DmmResponse($this->request, $graphResponse, 401);

        $exception = $response->getThrownException();

        $this->assertTrue($response->isError(), 'Expected Response to return an error.');
        $this->assertInstanceOf('Dmm\Exceptions\DmmResponseException', $exception);
    }
}
