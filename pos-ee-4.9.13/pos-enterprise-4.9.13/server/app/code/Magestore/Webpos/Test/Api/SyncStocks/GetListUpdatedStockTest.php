<?php
/**
 *
 */
namespace Magestore\Webpos\Test\Api\SyncStocks;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Webapi\Exception;

use Magestore\Webpos\Test\Constant\Product;
use Magestore\Webpos\Test\Api\GetSessionTrait;

class GetListUpdatedStockTest extends WebapiAbstract
{
    use GetSessionTrait;

    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/webpos/stocks/sync';
    const SERVICE_NAME = 'stocksSyncRepositoryV1';

    /**
     * @var
     */
    protected $posSession;

    protected $apiName = "getListUpdatedStocks";

    /**
     * @return false|string
     */
    protected function setUp()
    {
        $this->posSession = $this->loginAndAssignPos();
    }

    /**
     * Initialize fixture namespaces.
     */
    public static function setUpBeforeClass()
    {
        include __DIR__. '/../../_files/update_product.php';
        parent::setUpBeforeClass();
    }

    /**
     * Run garbage collector for cleaning memory
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        /* remove sample Data1 */
        include __DIR__. '/../../_files/update_product_rollback.php';
        parent::tearDownAfterClass();
    }

    /**
     * Test Case SS1 - No items need to sync from sample data after created
     */
    public function testCase1()
    {
        //$timeAfterCreate = $this->getTimeAfterCreated();
        $timeAfterCreate = $this->getTimeAfterCreatedAndUpdated();
        $requestData = [
            'searchCriteria' => [
                SearchCriteria::FILTER_GROUPS => [
                    [
                        'filters' => [
                            [
                                'field' => 'updated_at',
                                'value' => $timeAfterCreate,
                                'condition_type' => 'gt',
                            ],
                        ],
                    ],
                ],
                SearchCriteria::PAGE_SIZE => 10,
                SearchCriteria::CURRENT_PAGE => 1
            ],
        ];
        $expectedTotalCount = 0;

        /* get Response from API test */
        $response = $this->getResponseAPI($requestData);

        $message = "API getListUpdatedStocks fail at testcase SS1";
        $this->assertNotNull($response, $message);

        /* check search_criteria */
        AssertArrayContains::assert($requestData['searchCriteria'], $response['search_criteria']);

        /* check totalcount = 0 */
        self::assertEquals($expectedTotalCount, $response['total_count'], $message);

        /* check list_items is empty */
        self::assertEmpty($response['items'] , $message);

    }

    /**#@-*/
    /**
     * Test Case SS2 - has 2 items need to sync from sample data after updated
     */
    public function testCase2()
    {
        $timeAtFirstUpdate = $this->getTimeAtFirstUpdated();
        $requestData = [
            'searchCriteria' => [
                SearchCriteria::FILTER_GROUPS => [
                    [
                        'filters' => [
                            [
                                'field' => 'updated_at',
                                'value' => $timeAtFirstUpdate,
                                'condition_type' => 'gteq',
                            ],
                        ],
                    ],
                ],
                SearchCriteria::PAGE_SIZE => 10,
                SearchCriteria::CURRENT_PAGE => 1
            ],
        ];
        $expectedTotalCount = 2;
        /* get Response from API test */
        $response = $this->getResponseAPI($requestData);


        $message = "API getListUpdatedStocks fail at testcase SS2";
        $this->assertNotNull($response, $message);

        /* check search_criteria */
        AssertArrayContains::assert($requestData['searchCriteria'], $response['search_criteria']);

        /* check totalcount = 2 */
        //self::assertEquals($expectedTotalCount, $response['total_count'], $message);

        /* change conditions : that not check the att visible_on_pos and product_status */
        self::assertEquals(3, $response['total_count'], $message);

        /* check list_items is not empty */
        self::assertNotEmpty($response['items'] , $message);

        /* change conditions : that not check the att visible_on_pos and product_status -> has 3 item instead of 2*/
        $expectedItemsData = [
            [
                'sku' => Product::SKU_8,
                //'status' => Status::STATUS_ENABLED,
            ],
            [
                'sku' => Product::SKU_13,
            ],
            [
                'sku' => Product::SKU_14,
                //'status' => Status::STATUS_ENABLED,
            ]
        ];
        /* check list_items is contains excepted data items */
        AssertArrayContains::assert($expectedItemsData, $response['items']);
    }

    /**
     * Test Case SS3 - the pos_session is not valid
     */
    public function testCase3()
    {
        $this->testCaseId = "SS3";
        $this->sessionCase1();
    }

    /**
     * Test Case SS4 - the pos_session is missing
     */
    public function testCase4()
    {
        $this->testCaseId = "SS4";
        $this->sessionCase2();
    }

    /**
     * Test Case SS5 - the searchCriteria is missing
     */
    public function testCase5()
    {
        $this->testCaseId = "SS5";
        $this->sessionCase3();
    }

}
