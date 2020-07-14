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


class SyncTest extends WebapiAbstract
{

    use GetSessionTrait;

    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/webpos/stocks/sync';
    const SERVICE_NAME = 'stocksSyncRepositoryV1';

    protected $posSession;

    protected $apiName = "syncStocks";

    protected function setUp()
    {
        $this->posSession = $this->loginAndAssignPos();
    }

    /**#@-*/
    /**
     * Test Case SS1 - No items need to get from sample data
     */
    public function testCase6()
    {
        /* change conditions to get all*/
        $listSkus  = array (Product::SKU_1,
                            Product::SKU_2,
                            Product::SKU_3,
                            Product::SKU_4,
                            Product::SKU_5,
                            Product::SKU_6,
                            Product::SKU_7,
                            Product::SKU_8,
                            Product::SKU_9,
                            Product::SKU_10,
                            Product::SKU_11,
                            Product::SKU_12);
        $listSkus  = implode(',',$listSkus);
        $requestData = [
            'searchCriteria' => [
                SearchCriteria::FILTER_GROUPS => [
                    [
                        'filters' => [
                            [
                                'field' => 'sku',
                                'value' => $listSkus,
                                'condition_type' => 'in',
                            ],
                        ],
                    ],
                ],
                SearchCriteria::PAGE_SIZE => 100,
                SearchCriteria::CURRENT_PAGE => 1
            ],
        ];

        return $expectedTotalCount = 0;

        /* get Response from API test */
        $response = $this->getResponseAPI($requestData);

        $message = "API getSyncStock fail at testcase SS6";
        $this->assertNotNull($response, $message);

        /* check search_criteria */
        AssertArrayContains::assert($requestData['searchCriteria'], $response['search_criteria']);

        /* check totalcount = 0 */
        self::assertEquals($expectedTotalCount, $response['total_count'] , $message);

        /* check list_items is null or empty */
        self::assertEmpty($response['items'] , $message);
    }


    /**#@-*/

    /**
     * Test Case SS2 - has 3 items need to sync from sample data
     */
    public function testCase7()
    {
        /* not check conditions - get all */
        $listSkus  = array (Product::SKU_1,
                            Product::SKU_2,
                            Product::SKU_3,
                            Product::SKU_4,
                            Product::SKU_5,
                            Product::SKU_6,
                            Product::SKU_7,
                            Product::SKU_8,
                            Product::SKU_9,
                            Product::SKU_10,
                            Product::SKU_11,
                            Product::SKU_12,
                            Product::SKU_13,
                            Product::SKU_14,
                            Product::SKU_15);
        $listSkus  = implode(',',$listSkus);
        $requestData = [
            'searchCriteria' => [
                SearchCriteria::FILTER_GROUPS => [
                    [
                        'filters' => [
                            [
                                'field' => 'sku',
                                'value' => $listSkus,
                                'condition_type' => 'in',
                            ],
                        ],
                    ],
                ],
                SearchCriteria::PAGE_SIZE => 100,
                SearchCriteria::CURRENT_PAGE => 1
            ],
        ];

        return $expectedTotalCount = 3;
        $response =  $this->getResponseAPI($requestData);


        $message = "API getSyncStock fail at testcase SS7";
        $this->assertNotNull($response, $message);


        /* check search_criteria */
        AssertArrayContains::assert($requestData['searchCriteria'], $response['search_criteria']);

        /* check totalcount = 3 */
        self::assertEquals($expectedTotalCount, $response['total_count'] , $message);

        /* check list_items is not empty */
        self::assertNotEmpty($response['items'] , $message);

        $expectedItemsData = [
            [
                'sku' => Product::SKU_13,
                'qty' => 130,
            ],
            [
                'sku' => Product::SKU_14,
                'qty' => 140,
            ],
            [
                'sku' => Product::SKU_15,
                'qty' => 150,
            ],
        ];
        /* check list_items is contains excepted data items */
        AssertArrayContains::assert($expectedItemsData, $response['items']);
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
