<?php

namespace Magestore\Webpos\Test\Data\CreateData;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\TestFramework\Assert\AssertArrayContains;

/**
 * Api Test SampleData
 */
class SampleData extends WebapiAbstract
{
    /**
     * Initialize fixture namespaces.
     */
    public static function setUpBeforeClass() : void // phpcs:ignore
    {
        /* create sample Data2 */
        include __DIR__. '/../../_files/product.php';
        include __DIR__. '/../../_files/source.php';
        include __DIR__. '/../../_files/source_item.php';
        include __DIR__. '/../../_files/stock.php';
        include __DIR__. '/../../_files/stock_source_link.php';
        parent::setUpBeforeClass();
    }

    /**
     * Test Case
     */
    public function testCase()
    {
        $expected = true;
        self::assertEquals($expected, $expected);
    }
}
