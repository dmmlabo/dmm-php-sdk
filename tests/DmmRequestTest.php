<?php
namespace Dmm\Tests;

use Dmm\Dmm;
use Dmm\DmmCredential;
use Dmm\DmmRequest;

class DmmRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testAnEmptyRequestEntityCanInstantiate()
    {
        $credential = new DmmCredential('123', 'foo_id');
        $request = new DmmRequest($credential);

        $this->assertInstanceOf('Dmm\DmmRequest', $request);
    }

    /**
     * @expectedException \Dmm\Exceptions\DmmSDKException
     */
    public function testAMissingAccessTokenWillThrow()
    {
        $credential = new DmmCredential('123', 'foo_id');
        $request = new DmmRequest($credential);

        $request->validateCredentials();
    }

    /**
     * @expectedException \Dmm\Exceptions\DmmSDKException
     */
    public function testAMissingMethodWillThrow()
    {
        $credential = new DmmCredential('123', 'foo_id');
        $request = new DmmRequest($credential);

        $request->validateMethod();
    }

    /**
     * @expectedException \Dmm\Exceptions\DmmSDKException
     */
    public function testAnInvalidMethodWillThrow()
    {
        $credential = new DmmCredential('123', 'foo_id');
        $request = new DmmRequest($credential, 'FOO');

        $request->validateMethod();
    }

    public function testGetParamsWillAutoAppendAffiliateIdAndApiId()
    {
        $credential = new DmmCredential('123', 'foo_id');
        $request = new DmmRequest($credential, 'GET', '/foo', ['foo' => 'bar']);

        $params = $request->getParams();

        $this->assertEquals([
            'foo' => 'bar',
            'affiliate_id' => '123',
            'api_id' => 'foo_id',
        ], $params);
    }

    public function testAProperUrlWillBeGenerated()
    {
        $credential = new DmmCredential('test-999', '12wdfgyuik');
        $getRequest = new DmmRequest($credential, 'GET', '/foo', ['foo' => 'bar']);

        $getUrl = $getRequest->getUrl();
        $expectedParams = 'foo=bar&affiliate_id=test-999&api_id=12wdfgyuik';
        $expectedUrl = '/foo?' . $expectedParams;

        $this->assertEquals($expectedUrl, $getUrl);
    }
}
