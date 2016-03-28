<?php
namespace Dmm\Tests\Url;

use Dmm\Url\DmmUrlManipulator;

class DmmUrlManipulatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideUris
     */
    public function testParamsGetRemovedFromAUrl($dirtyUrl, $expectedCleanUrl)
    {
        $removeParams = [
            'state',
            'code',
            'error',
            'error_reason',
            'error_description',
            'error_code',
        ];
        $currentUri = DmmUrlManipulator::removeParamsFromUrl($dirtyUrl, $removeParams);
        $this->assertEquals($expectedCleanUrl, $currentUri);
    }

    public function provideUris()
    {
        return [
            [
                'http://localhost/something?state=0000&foo=bar&code=abcd',
                'http://localhost/something?foo=bar',
            ],
            [
                'https://localhost/something?state=0000&foo=bar&code=abcd',
                'https://localhost/something?foo=bar',
            ],
            [
                'http://localhost/something?state=0000&foo=bar&error=abcd&error_reason=abcd&error_description=abcd&error_code=1',
                'http://localhost/something?foo=bar',
            ],
            [
                'https://localhost/something?state=0000&foo=bar&error=abcd&error_reason=abcd&error_description=abcd&error_code=1',
                'https://localhost/something?foo=bar',
            ],
            [
                'http://localhost/something?state=0000&foo=bar&error=abcd',
                'http://localhost/something?foo=bar',
            ],
            [
                'https://localhost/something?state=0000&foo=bar&error=abcd',
                'https://localhost/something?foo=bar',
            ],
            [
                'https://localhost:1337/something?state=0000&foo=bar&error=abcd',
                'https://localhost:1337/something?foo=bar',
            ],
            [
                'https://localhost:1337/something?state=0000&code=foo',
                'https://localhost:1337/something',
            ],
            [
                'https://localhost/something/?state=0000&code=foo&foo=bar',
                'https://localhost/something/?foo=bar',
            ],
            [
                'https://localhost/something/?state=0000&code=foo',
                'https://localhost/something/',
            ],
        ];
    }

    public function testGracefullyHandlesUrlAppending()
    {
        $params = [];
        $url = 'https://www.foo.com/';
        $processed_url = DmmUrlManipulator::appendParamsToUrl($url, $params);
        $this->assertEquals('https://www.foo.com/', $processed_url);

        $params = [
            'api_id' => 'foo',
        ];
        $url = 'https://www.foo.com/';
        $processed_url = DmmUrlManipulator::appendParamsToUrl($url, $params);
        $this->assertEquals('https://www.foo.com/?api_id=foo', $processed_url);

        $params = [
            'api_id' => 'foo',
            'bar' => 'baz',
        ];
        $url = 'https://www.foo.com/?foo=bar';
        $processed_url = DmmUrlManipulator::appendParamsToUrl($url, $params);
        $this->assertEquals('https://www.foo.com/?api_id=foo&bar=baz&foo=bar', $processed_url);

        $params = [
            'api_id' => 'foo',
        ];
        $url = 'https://www.foo.com/?foo=bar&api_id=bar';
        $processed_url = DmmUrlManipulator::appendParamsToUrl($url, $params);
        $this->assertEquals('https://www.foo.com/?api_id=bar&foo=bar', $processed_url);
    }

    public function testSlashesAreProperlyPrepended()
    {
        $slashTestOne   = DmmUrlManipulator::forceSlashPrefix('foo');
        $slashTestTwo   = DmmUrlManipulator::forceSlashPrefix('/foo');
        $slashTestThree = DmmUrlManipulator::forceSlashPrefix('foo/bar');
        $slashTestFour  = DmmUrlManipulator::forceSlashPrefix('/foo/bar');
        $slashTestFive  = DmmUrlManipulator::forceSlashPrefix(null);
        $slashTestSix   = DmmUrlManipulator::forceSlashPrefix('');

        $this->assertEquals('/foo', $slashTestOne);
        $this->assertEquals('/foo', $slashTestTwo);
        $this->assertEquals('/foo/bar', $slashTestThree);
        $this->assertEquals('/foo/bar', $slashTestFour);
        $this->assertEquals(null, $slashTestFive);
        $this->assertEquals('', $slashTestSix);
    }

    public function testParamsCanBeReturnedAsArray()
    {
        $paramsOne   = DmmUrlManipulator::getParamsAsArray('/foo');
        $paramsTwo   = DmmUrlManipulator::getParamsAsArray('/foo?one=1&two=2');
        $paramsThree = DmmUrlManipulator::getParamsAsArray('https://www.foo.com');
        $paramsFour  = DmmUrlManipulator::getParamsAsArray('https://www.foo.com/?');
        $paramsFive  = DmmUrlManipulator::getParamsAsArray('https://www.foo.com/?foo=bar');

        $this->assertEquals([], $paramsOne);
        $this->assertEquals(['one' => '1', 'two' => '2'], $paramsTwo);
        $this->assertEquals([], $paramsThree);
        $this->assertEquals([], $paramsFour);
        $this->assertEquals(['foo' => 'bar'], $paramsFive);
    }

    /**
     * @dataProvider provideMergableEndpoints
     */
    public function testParamsCanBeMergedOntoUrlProperly($urlOne, $urlTwo, $expected)
    {
        $result = DmmUrlManipulator::mergeUrlParams($urlOne, $urlTwo);

        $this->assertEquals($result, $expected);
    }

    public function provideMergableEndpoints()
    {
        return [
            [
                'https://www.foo.com/?foo=ignore_foo&dance=fun',
                '/me?foo=keep_foo',
                '/me?dance=fun&foo=keep_foo',
            ],
            [
                'https://www.bar.com?',
                'https://foo.com?foo=bar',
                'https://foo.com?foo=bar',
            ],
            [
                'you',
                'me',
                'me',
            ],
            [
                '/1234?swing=fun',
                '/1337?bar=baz&west=coast',
                '/1337?bar=baz&swing=fun&west=coast',
            ],
        ];
    }

    public function testGraphUrlsCanBeTrimmed()
    {
        $fullApiUrl = 'https://api.dmm.com/affiliate/v3/';
        $baseApiUrl = DmmUrlManipulator::baseApiUrlEndpoint($fullApiUrl);
        $this->assertEquals('/', $baseApiUrl);

        $fullApiUrl = 'https://api.dmm.com/affiliate/v3/ItemList';
        $baseApiUrl = DmmUrlManipulator::baseApiUrlEndpoint($fullApiUrl);
        $this->assertEquals('/ItemList', $baseApiUrl);

        $fullApiUrl = 'https://api.dmm.com/affiliate/v3/1233?foo=bar';
        $baseApiUrl = DmmUrlManipulator::baseApiUrlEndpoint($fullApiUrl);
        $this->assertEquals('/1233?foo=bar', $baseApiUrl);
    }
}
