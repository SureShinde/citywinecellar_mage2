<?php
/**
 *
 */

namespace Magestore\Webpos\Test\Api\SyncProducts;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Webapi\Exception;


use Magestore\Webpos\Test\Api\GetSessionTrait;
use Magestore\Webpos\Test\Constant\Product;

/**
 * Api Test GetListUpdatedProductTest
 */
class GetListUpdatedProductTest extends WebapiAbstract
{
    use GetSessionTrait;

    /**
     * Service constants
     */
    const RESOURCE_PATH = '/V1/webpos/products/sync';
    const SERVICE_NAME = 'productsSyncUpdatedRepositoryV1';

    /**
     * @var
     */
    protected $posSession;

    protected $apiName = "getListUpdatedProducts";

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
     * Initialize fixture namespaces.
     */
    public static function setUpBeforeClass() : void // phpcs:ignore
    {
        include __DIR__. '/../../_files/update_product.php';
        parent::setUpBeforeClass();
    }

    /**
     * Run garbage collector for cleaning memory
     *
     * @return void
     */
    public static function tearDownAfterClass() : void // phpcs:ignore
    {
        include __DIR__. '/../../_files/update_product_rollback.php';
        parent::tearDownAfterClass();
    }

    /**
     * Test Case SP1 - No items need to sync from sample data after created
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

        $message = "API getListUpdatedProducts fail at testcase SP1";
        $this->assertNotNull($response, $message);

        /* check search_criteria */
        AssertArrayContains::assert($requestData['searchCriteria'], $response['search_criteria']);

        /* check totalcount = 0 */
        self::assertEquals($expectedTotalCount, $response['total_count'], $message);

        /* check list_items is empty */
        self::assertEmpty($response['items'], $message);
    }

    /**
     * Test Case SP2 - has 2 items need to sync from sample data after updated
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

        $message = "API getListUpdatedProducts fail at testcase SP2";
        $this->assertNotNull($response, $message);

        /* check search_criteria */
        AssertArrayContains::assert($requestData['searchCriteria'], $response['search_criteria']);

        /* check totalcount = 2 */
        self::assertEquals($expectedTotalCount, $response['total_count'], $message);

        /* check list_items is not empty */
        self::assertNotEmpty($response['items'], $message);

        $expectedItemsData = [
            [
                'sku' => Product::SKU_8,
                'status' => Status::STATUS_ENABLED,
            ],
            [
                'sku' => Product::SKU_14,
                'status' => Status::STATUS_ENABLED,
            ]
        ];
        /* check list_items is contains excepted data items */
        AssertArrayContains::assert($expectedItemsData, $response['items']);
    }

    /**
     * Test Case SP3 - the pos_session is not valid
     */
    public function testCase3()
    {
        $this->testCaseId = "SP3";
        $this->sessionCase1();
    }

    /**
     * Test Case SP4 - the pos_session is missing
     */
    public function testCase4()
    {
        $this->testCaseId = "SP4";
        $this->sessionCase2();
    }

    /**
     * Test Case SP5 - the searchCriteria is missing
     */
    public function testCase5()
    {
        $this->testCaseId = "SP5";
        $this->sessionCase3();
    }
}
