<?php
namespace Dmm\Tests\Exceptions;

use Dmm\DmmCredential;
use Dmm\DmmRequest;
use Dmm\DmmResponse;
use Dmm\Exceptions\DmmResponseException;

class DmmResponseExceptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DmmRequest
     */
    protected $request;

    public function setUp()
    {
        $this->request = new DmmRequest(new DmmCredential('123', 'foo_id'));
    }

    public function testOtherException()
    {
        $params = [
            "status"  => 500,
            "message" => "So Hungry!!"
        ];
        $response = new DmmResponse($this->request, json_encode($params), 200);
        $exception =  DmmResponseException::create($response);
        $this->assertInstanceOf('Dmm\Exceptions\DmmOtherException', $exception->getPrevious());
        $this->assertEquals(500, $exception->getCode());
        $this->assertEquals('So Hungry!!', $exception->getMessage());
        $this->assertEquals(json_encode($params), $exception->getRawResponse());
        $this->assertEquals($response, $exception->getResponse());
        $this->assertEquals($params, $exception->getResponseData());
        $this->assertEquals(200, $exception->getHttpStatusCode());
    }
}
