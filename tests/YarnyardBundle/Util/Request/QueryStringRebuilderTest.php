<?php

namespace Tests\YarnyardBundle\Util\Request;

use YarnyardBundle\Util\Request\QueryStringRebuilder;

class QueryStringRebuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider urlPartsProvider
     *
     * @param $existingUrl
     * @param $newParts
     * @param $expected
     */
    public function addQueryWillWorkAsExpected($existingUrl, $newParts, $expected)
    {
        $helper = new QueryStringRebuilder();
        $newUrl = $helper->addQueryToUrl($existingUrl, $newParts);

        $this->assertEquals($expected, $newUrl);
    }

    /**
     * @return array
     */
    public function urlPartsProvider()
    {
        return [
            [
                'http://www.foo.com',
                [],
                'http://www.foo.com',
            ],
            [
                'http://www.foo.com',
                ['foo' => 'bar'],
                'http://www.foo.com?foo=bar',
            ],
            [
                'http://www.foo.com?old=this',
                ['foo' => 'bar'],
                'http://www.foo.com?old=this&foo=bar',
            ],
            [
                'http://www.foo.com#something',
                ['foo' => 'bar'],
                'http://www.foo.com?foo=bar#something',
            ],
            [
                'http://www.foo.com#something/in/here/index.html?first=1&second=true#anchor-bla',
                ['foo' => 'bar'],
                'http://www.foo.com#something/in/here/index.html?first=1&second=true&foo=bar#anchor-bla',
            ]
        ];
    }
}