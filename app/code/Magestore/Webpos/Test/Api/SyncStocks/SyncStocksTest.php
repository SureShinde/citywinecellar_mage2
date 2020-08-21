<?php
/**
 *
 */

namespace Magestore\Webpos\Test\Api\SyncStocks;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Exception;
use Magento\TestFramework\Helper\Bootstrap;


use Magestore\Webpos\Test\Api\GetSessionTrait;
use Magestore\Webpos\Test\Constant\Product;

/**
 * Api test SyncStocksTest
 */
class SyncStocksTest extends WebapiAbstract
{

    use GetSessionTrait;

    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/webpos/stocks/sync';
    const SERVICE_NAME = 'stocksSyncRepositoryV1';

    protected $posSession;

    protected $apiName = "syncStocks";

    /**
     * Setup
     */
    protected function setUp() : void // phpcs:ignore
    {
        $this->posSession = $this->loginAndAssignPos();
    }

    /**#@-*/
    /**
     * Test Case SS1 - No items need to get from sample data
     */
    public function testCase6()
    {
        return 0;
    }

    /**
     * Test Case SS2 - has 3 items need to sync from sample data
     */
    public function testCase7()
    {
        return 3;
    }

    /**
     * Test Case SS3 - the pos_session is not valid
     */
    public function testCase8()
    {
        $this->testCaseId = "SS8";
        $this->sessionCase1();
    }

    /**
     * Test Case SS4 - the pos_session is missing
     */
    public function testCase9()
    {
        $this->testCaseId = "SS9";
        $this->sessionCase2();
    }

    /**
     * Test Case SS5 - the searchCriteria is missing
     */
    public function testCase10()
    {
        $this->testCaseId = "SS10";
        $this->sessionCase3();
    }
}
