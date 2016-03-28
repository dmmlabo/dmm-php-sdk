<?php
namespace Dmm\Tests;

use Dmm\DmmCredential;

class DmmCredentialTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DmmCredential
     */
    private $credential;

    public function setUp()
    {
        $this->credential = new DmmCredential('test-990', 'foo_bar');
    }

    public function testGetAffiliateId()
    {
        $this->assertEquals('test-990', $this->credential->getAffiliateId());
    }

    public function testGetApiId()
    {
        $this->assertEquals('foo_bar', $this->credential->getApiId());
    }
}
