<?php

namespace Magestore\Webpos\Test\Data\RemoveData;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\TestFramework\Assert\AssertArrayContains;

/**
 * Api Test Remove Sample Data
 */
class SampleData extends WebapiAbstract
{
    /**
     * Run garbage collector for cleaning memory
     *
     * @return void
     */
    public static function tearDownAfterClass() : void // phpcs:ignore
    {
        /* remove sample Data1 */
        include __DIR__. '/../../_files/product_rollback.php';
        include __DIR__. '/../../_files/source_rollback.php';
        include __DIR__. '/../../_files/source_item_rollback.php';
        include __DIR__. '/../../_files/stock_rollback.php';
        include __DIR__. '/../../_files/stock_source_link_rollback.php';
        parent::tearDownAfterClass();
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
