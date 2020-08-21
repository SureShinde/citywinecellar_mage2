<?php

/**
 *
 */
namespace Magestore\Webpos\Test\Api\GetCategories;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Webapi\Exception;

use Magestore\Webpos\Test\Api\GetSessionTrait;

/**
 * Api Test GetCategoriesTest
 */
class GetCategoriesTest extends WebapiAbstract
{
    use GetSessionTrait;

    /**
     * Service constants
     */
    const RESOURCE_PATH = '/V1/webpos/categories';
    const SERVICE_NAME = 'categoriesGetRepositoryV1';

    /**
     * @var
     */
    protected $posSession;

    protected $apiName = "getCategories";

    /**
     * Set Up
     * @return false|string
     */
    protected function setUp() : void // phpcs:ignore
    {
        $this->posSession = $this->loginAndAssignPos();
    }

    /**
     * Test Case GC1 - No items need to sync from sample data
     */
    public function testCase1()
    {
        $requestData = [
            'searchCriteria' => [
                SearchCriteria::FILTER_GROUPS => [
                    [
                        'filters' => [
                            [
                                'field' => 'parent_id',
                                'value' => '99999',
                                'condition_type' => 'eq',
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

        $message = "API getCategories fail at testcase GC1";
        $this->assertNotNull($response, $message);

        /* check search_criteria */
        AssertArrayContains::assert($requestData['searchCriteria'], $response['search_criteria']);

        /* check totalcount = 0 */
        self::assertEquals($expectedTotalCount, $response['total_count'], $message);

        /* check list_items is null or empty */
        self::assertEmpty($response['items'], $message);
    }

    /**
     * Test Case GC2 - has 3 items need to sync from sample data
     */
    public function testCase2()
    {
        $requestData = [
            'searchCriteria' => [
                SearchCriteria::FILTER_GROUPS => [
                    [
                        'filters' => [
                            [
                                'field' => 'parent_id',
                                'value' => '3',
                                'condition_type' => 'eq',
                            ],
                        ],
                    ],
                ],
                SearchCriteria::PAGE_SIZE => 10,
                SearchCriteria::CURRENT_PAGE => 1
            ],
        ];
        $expectedTotalCount = 3;
        $response =  $this->getResponseAPI($requestData);

        $message = "API getCategories fail at testcase GC2";
        $this->assertNotNull($response, $message);

        /* check search_criteria */
        AssertArrayContains::assert($requestData['searchCriteria'], $response['search_criteria']);

        /* check totalcount = 3 */
        self::assertEquals($expectedTotalCount, $response['total_count'], $message);

        /* check list_items is not empty */
        self::assertNotEmpty($response['items'], $message);

        $expectedItemsData = [
            [
                'id' => 4,
                'name' => "Bags",
                'parent_id' => 3,
            ],
            [
                'id' => 5,
                'name' => "Fitness Equipment",
                'parent_id' => 3,
            ],
            [
                'id' => 6,
                'name' => "Watches",
                'parent_id' => 3,
            ],
        ];

        /* check list_items is contains excepted data items */
        AssertArrayContains::assert($expectedItemsData, $response['items']);
    }

    /**
     * Test Case GC3 - the pos_session is not valid
     */
    public function testCase3()
    {
        $this->testCaseId = "GC3";
        $this->sessionCase1();
    }

    /**
     * Test Case GC4 - the pos_session is missing
     */
    public function testCase4()
    {
        $this->testCaseId = "GC4";
        $this->sessionCase2();
    }

    /**
     * Test Case GC5 - the searchCriteria is missing
     */
    public function testCase5()
    {
        $this->testCaseId = "GC5";
        $this->sessionCase3();
    }
}
