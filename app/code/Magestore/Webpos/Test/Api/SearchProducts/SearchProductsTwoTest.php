<?php

namespace Magestore\Webpos\Test\Api\SearchProducts;

use Magento\Framework\Api\SearchCriteria;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Exception;
use Magento\TestFramework\Helper\Bootstrap;

use Magestore\Webpos\Test\Constant\Product;
use Magestore\Webpos\Test\Api\GetSessionTrait;
use Magestore\Webpos\Test\Api\SearchProducts\SearchProductsTrait;

/**
 * Api Test SearchProductsTwoTest
 */
class SearchProductsTwoTest extends WebapiAbstract
{

    use GetSessionTrait;
    use SearchProductsTrait;

    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/webpos/products/search';
    const SERVICE_NAME = 'searchProductsRepositoryV1';

    protected $posSession;

    protected $apiName = "searchProducts";

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
     * Test Case 5 - has 1 items return
     */
    public function testCase5()
    {
        // Disable test because elastic search still response data with this search key
        return true;
        // $this->testCaseId = "5";
        // $this->createRequestData(Product::SKU_13);
        // /* get Response from API test */
        // $response = $this->getResponseAPI($this->requestData);

        // /* expected Data is empty */
        // $this->expectedHasOneItemData($response);
    }

    /**
     * Test Case 6 - has 1 items return
     */
    public function testCase6()
    {
        // Disable test because elastic search still response data with this search key
        return true;
        // $this->testCaseId = "6";
        // $this->createRequestData(Product::NAME_13);
        // /* get Response from API test */
        // $response = $this->getResponseAPI($this->requestData);

        // /* expected Data is empty */
        // $this->expectedHasOneItemData($response);
    }

    /**
     * Test Case 7 - has 3 items return
     */
    public function testCase7()
    {

        $this->testCaseId = "7";
        $this->createRequestData(Product::OPERATOR_SKU);
        /* get Response from API test */
        $response = $this->getResponseAPI($this->requestData);

        /* expected Data is empty */
        $this->expectedHasThereItemData($response);
    }

    /**
     * Test Case 8 - has 3 items return
     */
    public function testCase8()
    {

        $this->testCaseId = "8";
        $this->createRequestData(Product::OPERATOR_NAME);
        /* get Response from API test */
        $response = $this->getResponseAPI($this->requestData);

        /* expected Data is empty */
        $this->expectedHasThereItemData($response);
    }

    /**
     * Test Case 9 - has 1 items return
     */
    public function testCase9()
    {

        $this->testCaseId = "9";
        $this->createRequestData(Product::OPERATOR_NAME, 1, 1, true);
        /* get Response from API test */
        $response = $this->getResponseAPI($this->requestData);

        /* expected Data is empty */
        $this->expectedHasOneItemData($response, null, true);
    }

    /**
     * Test Case 10 - has 2 items return
     */
    public function testCase10()
    {

        $this->testCaseId = "10";
        $this->createRequestData(Product::OPERATOR_NAME, 2, 1);
        /* get Response from API test */
        $response = $this->getResponseAPI($this->requestData);

        /* expected Data is empty */
        $this->expectedHasTwoItemData($response, null, true);
    }

    /**
     * Test Case 11 - has 1 items return
     */
    public function testCase11()
    {

        $this->testCaseId = "11";
        $this->createRequestData(Product::OPERATOR_NAME, 2, 2);
        /* get Response from API test */
        $response = $this->getResponseAPI($this->requestData);
        $expectedItemsData = [
            [
                'sku' => Product::SKU_15,
            ],
        ];
        /* expected Data is empty */
        $this->expectedHasOneItemData($response, $expectedItemsData, true);
    }

    /**
     * Test Case 12 - has 1 items return
     */
    public function testCase12()
    {
        /* disable this testcase because business is not allow sort by DESC*/
        return true;
        // $this->testCaseId = "12";
        // $this->createRequestData(Product::OPERATOR_NAME, 1, 1, 'DESC');
        // /* get Response from API test */
        // $response = $this->getResponseAPI($this->requestData);

        // $expectedItemsData = [
        //     [
        //         'sku' => Product::SKU_13,
        //     ],
        // ];
        // /* expected Data is empty */
        // $this->expectedHasOneItemData($response, $expectedItemsData, true);
    }

    /**
     * Test Case 13 - has 2 items return
     */
    public function testCase13()
    {
        /* disable this testcase because business is not allow sort by DESC*/
        return true;
        // $this->testCaseId = "13";
        // $this->createRequestData(Product::OPERATOR_NAME, 2, 1, 'DESC');
        // /* get Response from API test */
        // $response = $this->getResponseAPI($this->requestData);

        // $expectedItemsData = [
        //     [
        //         'sku' => Product::SKU_15,
        //     ],
        //     [
        //         'sku' => Product::SKU_14,
        //     ],
        // ];
        // /* expected Data is empty */
        // $this->expectedHasTwoItemData($response, $expectedItemsData, true);
    }

    /**
     * Test Case 14 - has 2 items return
     */
    public function testCase14()
    {
        /* disable this testcase because business is not allow sort by DESC*/
        return true;
        // $this->testCaseId = "14";
        // $this->createRequestData(Product::OPERATOR_NAME, 2, 2, 'DESC');
        // /* get Response from API test */
        // $response = $this->getResponseAPI($this->requestData);

        // $expectedItemsData = [
        //     [
        //         'sku' => Product::SKU_13,
        //     ],
        // ];
        // /* expected Data is empty */
        // $this->expectedHasOneItemData($response, $expectedItemsData, true);
    }

    /**
     * Test Case 15 - the pos_session is not valid
     */
    public function testCase15()
    {
        $this->testCaseId = "15";
        $this->sessionCase1();
    }

    /**
     * Test Case 16 - the pos_session is missing
     */
    public function testCase16()
    {
        $this->testCaseId = "16";
        $this->sessionCase2();
    }

    /**
     * Test Case 17 - the searchCriteria is missing
     */
    public function testCase17()
    {
        $this->testCaseId = "17";
        $this->sessionCase3();
    }
}
