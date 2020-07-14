<?php

/**
 *
 */
namespace Magestore\Webpos\Test\Api\DeletedProducts;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Framework\Registry;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

use Magestore\Webpos\Test\Api\GetSessionTrait;
use Magestore\Webpos\Test\Api\DeletedProducts\DeletedProductsTrait;
use Magestore\Webpos\Test\Constant\Product;

class DeletedProductsTest extends WebapiAbstract
{

    use GetSessionTrait;
    use DeletedProductsTrait;

    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/webpos/products/deleted';
    const SERVICE_NAME = 'deletedProductsRepositoryV1';
    /**
     * @var
     */
    protected $posSession;

    /**
     * @var string
     */
    protected $apiName = "deletedProducts";

    /**
     * @var
     */
    protected $productRepository;

    /**
     * @var
     */
    protected $time;
    /**
     *
     */
    protected function setUp()
    {
        $this->posSession = $this->loginAndAssignPos();
        /** @var ProductRepositoryInterface $productRepository */
        $this->productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
        $this->time  = $this->getTimeAfterCreated();
        $this->deleteTable();
    }

    /**
     * Rollback updated when testing is done!
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        $productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
        /** SKU-11 */
        $product = $productRepository->get(Product::SKU_11);
        $product->setStatus(Status::STATUS_ENABLED);
        $product->save($product);
        /** SKU-12 */
        $product = $productRepository->get(Product::SKU_12);
        $product->setWebposVisible(1);
        $product->save($product);
        /** SKU-13 */
        $product = $productRepository->get(Product::SKU_13);
        $product->setStatus(Status::STATUS_ENABLED);
        $product->save($product);
        /** SKU-14 */
        $product = $productRepository->get(Product::SKU_14);
        $product->setWebposVisible(1);
        $product->save($product);
        /** SKU-15 */
        $product = $productRepository->get(Product::SKU_15);
        $product->setName(Product::NAME_15);
        $product->save($product);
        $productRepository->cleanCache();

        parent::tearDownAfterClass();
    }

    /**
     * Not change anything on sample Data
     */
    public function testCase1(){
        $this->testCaseId = "1";

        $requestData = $this->createRequestData($this->time);
        /* get Response from API test */
        $response = $this->getResponseAPI($requestData);

        $expectedTotalCount = 0;

        $message = sprintf('Failed at Testcase  "%s" ', $this->testCaseId);

        $this->assertNotNull($response, $message);

        /* chek items count is 0 or not */
        self::assertCount($expectedTotalCount, $response['ids'] , $message);
    }

    /**
     * Update Product not in Default Stock
     * SKU-11 : disabled
     * SKU-12 : not visible on pos
     */
    public function testCase2(){
        /** SKU-11 */
        $product = $this->productRepository->get(Product::SKU_11);
        $product->setStatus(Status::STATUS_DISABLED);
        $product->save($product);
        /** SKU-12 */
        $product = $this->productRepository->get(Product::SKU_12);
        $product->setWebposVisible(0);
        $product->save($product);

        $this->testCaseId = "2";

        $requestData = $this->createRequestData($this->time);
        /* get Response from API test */
        $response = $this->getResponseAPI($requestData);

        $expectedTotalCount = 2;

        $message = sprintf('Failed at Testcase  "%s" ', $this->testCaseId);

        $this->assertNotNull($response, $message);

        /* chek items count is 0 or not */
        //self::assertCount($expectedTotalCount, $response['ids'] , $message);

        /* change conditions that get all delete for all Stock */
        self::assertCount($expectedTotalCount, $response['ids'] , $message);
    }

    /**
     * Updated Product in Default Stock
     * SKU-13 : disabled
     * SKU-14 : not visible on pos
     * SKU-15 : change name
     */
    public function testCase3(){
        $idsExpected = array();
        /** SKU-13 */
        $product = $this->productRepository->get(Product::SKU_13);
        $product->setStatus(Status::STATUS_DISABLED);
        $product->save($product);
        $idsExpected [] = $product->getId();
        /** SKU-14 */
        $product = $this->productRepository->get(Product::SKU_14);
        $product->setWebposVisible(0);
        $product->save($product);
        $idsExpected [] = $product->getId();
        /** SKU-15 */
        $product = $this->productRepository->get(Product::SKU_15);
        $product->setName(Product::UPDATED_NAME_SKU_15);
        $product->save($product);

        $this->testCaseId = "3";

        $requestData = $this->createRequestData($this->time);
        /* get Response from API test */
        $response = $this->getResponseAPI($requestData);

        $this->updateTimeForNextTestCase();

        $expectedTotalCount = 2;

        $message = sprintf('Failed at Testcase  "%s" ', $this->testCaseId);

        $this->assertNotNull($response, $message);

        /* chek items count is 0 or not */
        //self::assertCount($expectedTotalCount, $response['ids'] , $message);

        /* check list_items is contains excepted data items */
        //AssertArrayContains::assert($idsExpected, $response['ids']);

        /* change conditions that get all delete for all Stock */
        self::assertCount($expectedTotalCount, $response['ids'] , $message);
    }

    /**
     * Updated Product in Default Stock
     * SKU-13 : enable
     * SKU-14 : visible on pos
     * SKU-15 : change name
     */
    public function testCase4(){
        /** SKU-13 */
        $product = $this->productRepository->get(Product::SKU_13);
        $product->setStatus(Status::STATUS_ENABLED);
        $product->save($product);
        /** SKU-14 */
        $product = $this->productRepository->get(Product::SKU_14);
        $product->setWebposVisible(1);
        $product->save($product);
        /** SKU-15 */
        $product = $this->productRepository->get(Product::SKU_15);
        $product->setName(Product::UPDATED_NAME_SKU_15);
        $product->save($product);

        $this->testCaseId = "4";

        $requestData = $this->createRequestData($this->time);
        /* get Response from API test */
        $response = $this->getResponseAPI($requestData);

        $expectedTotalCount = 0;
        
        $message = sprintf('Failed at Testcase  "%s" ', $this->testCaseId);

        $this->assertNotNull($response, $message);

        /* chek items count is 0 or not */
        self::assertCount($expectedTotalCount, $response['ids'] , $message);

    }

    /**
     * Test Case 5 - the pos_session is not valid
     */
    public function testCase5()
    {
        $this->testCaseId = "5";
        $this->sessionCase1();
    }

    /**
     * Test Case 6 - the pos_session is missing
     */
    public function testCase6()
    {
        $this->testCaseId = "6";
        $this->sessionCase2();
    }

    /**
     * Test Case 7 - the searchCriteria is missing
     */
    public function testCase7()
    {
        $this->testCaseId = "7";
        $this->sessionCase3();

    }
}
