<?php
/**
 *
 */
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
 * Class SearchProductsOneTest
 * @package Magestore\Webpos\Test\Api\SearchProducts
 */
class SearchProductsOneTest extends WebapiAbstract
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

    protected function setUp()
    {
        $this->posSession = $this->loginAndAssignPos();
    }

    /**
     * Initialize fixture namespaces.
     */
    public static function setUpBeforeClass()
    {
        include __DIR__ . '/../../_files/delete_product.php';
        parent::setUpBeforeClass();
    }
    /**
     * Run garbage collector for cleaning memory
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        include __DIR__ . '/../../_files/delete_product_rollback.php';
    }

    /**
     * Test Case 1 - No items return
     */
    public function testCase1(){

        $this->testCaseId = "1";
        $this->createRequestData(Product::SKU_10);
        /* get Response from API test */
        $response = $this->getResponseAPI($this->requestData);

        /* expected Data is empty */
        $this->expectedEmptyData($response);
    }

    /**
     * Test Case 2 - No items return
     */
    public function testCase2(){

        $this->testCaseId = "2";
        $this->createRequestData(Product::NAME_10);
        /* get Response from API test */
        $response = $this->getResponseAPI($this->requestData);

        /* expected Data is empty */
        $this->expectedEmptyData($response);

    }

    /**
     * Test Case 3 - No items return
     */
    public function testCase3(){

        $this->testCaseId = "3";
        $this->createRequestData(Product::OPERATOR_SKU);
        /* get Response from API test */
        $response = $this->getResponseAPI($this->requestData);

        /* expected Data is empty */
        $this->expectedEmptyData($response);
    }

    /**
     * Test Case 4 - No items return
     */
    public function testCase4(){

        $this->testCaseId = "4";
        $this->createRequestData(Product::OPERATOR_NAME);
        /* get Response from API test */
        $response = $this->getResponseAPI($this->requestData);

        /* expected Data is empty */
        $this->expectedEmptyData($response);
    }
}
