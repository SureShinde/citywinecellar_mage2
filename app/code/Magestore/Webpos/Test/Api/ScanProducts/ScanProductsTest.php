<?php

namespace Magestore\Webpos\Test\Api\ScanProducts;

use Magento\Framework\Api\SearchCriteria;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Exception;
use Magento\TestFramework\Helper\Bootstrap;

use Magestore\Webpos\Test\Constant\Product;
use Magestore\Webpos\Test\Api\GetSessionTrait;
use Magestore\Webpos\Test\Api\ScanProducts\ScanProductsTrait;

/**
 * Api Test ScanProductsTest
 */
class ScanProductsTest extends WebapiAbstract
{

    use GetSessionTrait;
    use ScanProductsTrait;

    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/webpos/products/barcode';
    const SERVICE_NAME = 'scanProductsRepositoryV1';

    protected $posSession;

    protected $apiName = "scanProducts";

    /**
     * Set Up
     *
     * @return void
     */
    protected function setUp() : void // phpcs:ignore
    {
        $this->posSession = $this->loginAndAssignPos();
    }

    /**
     * Test Case 1 - No items return
     */
    public function testCase1()
    {

        $this->testCaseId = "SB1";
        $this->createRequestData(Product::SKU_1);
        /* get Response from API test */
        $response = $this->getResponseAPI($this->requestData);

        /* expected Data is empty */
        $this->expectedEmptyData($response);
    }

    /**
     * Test Case 2 - No items return
     */
    public function testCase2()
    {

        $this->testCaseId = "SB2";
        $this->createRequestData('SKU-m');
        /* get Response from API test */
        $response = $this->getResponseAPI($this->requestData);
        /* expected Data is empty */
        $this->expectedEmptyData($response);
    }

    /**
     *
     */
    public function testCase3()
    {
        return true;
        // $this->testCaseId = "SB3";
        // $this->createRequestData(Product::SKU_13);
        // /* get Response from API test */
        // $response = $this->getResponseAPI($this->requestData);
        // /* expected Data is empty */
        // $this->expectedHasOneItemData($response);
    }

    /**
     * Test Case 4 - has no items return
     */
    public function testCase4()
    {

        $this->testCaseId = "SB4";
        $this->createRequestData('SKU-m');
        /* get Response from API test */
        $response = $this->getResponseAPI($this->requestData);

        /* expected Data is empty */
        $this->expectedEmptyData($response);
    }

    /**
     * Test Case 5 - the pos_session is not valid
     */
    public function testCase5()
    {
        $this->testCaseId = "SB5";
        $this->sessionCase1();
    }

    /**
     * Test Case 6 - the pos_session is missing
     */
    public function testCase6()
    {
        $this->testCaseId = "SB6";
        $this->sessionCase2();
    }

    /**
     * Test Case 7 - the searchCriteria is missing
     */
    public function testCase7()
    {
        $this->testCaseId = "SB7";
        $this->sessionCase3();
    }
}
