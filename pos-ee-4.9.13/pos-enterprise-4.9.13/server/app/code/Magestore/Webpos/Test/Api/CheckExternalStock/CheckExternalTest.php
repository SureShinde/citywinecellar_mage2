<?php

namespace Magestore\Webpos\Test\Api\CheckExternalStock;

use Magento\Framework\Api\SearchCriteria;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Exception;
use Magento\TestFramework\Helper\Bootstrap;

use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magestore\Webpos\Test\Api\GetSessionTrait;
use Magento\Catalog\Api\ProductRepositoryInterface;

use Magestore\Webpos\Test\Constant\Product;
use Magestore\Webpos\Test\Constant\Location;

class CheckExternalTest extends WebapiAbstract
{

    use GetSessionTrait;

    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/webpos/getExternalStock';
    const SERVICE_NAME = 'checkExternalStockV1';

    protected $posSession;
    protected $timeZone;
    protected $apiName = "checkExternalStock";

    protected $productId;

    /**
     * @var
     */
    protected $productRepository;

    protected function setUp()
    {
        $this->posSession = $this->loginAndAssignPos();
        $this->timeZone = Bootstrap::getObjectManager()->get('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $this->productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);

        $productSku = Product::SKU_13;
        $product = $this->productRepository->get($productSku);
        $this->productId = $product->getId();
    }

    /**
     * Test Case 1
     */
    public function testCase1(){
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH
                    .'/'.$this->productId.'?pos_session=' . $this->posSession,
                'httpMethod' => RestRequest::HTTP_METHOD_GET,
            ]
        ];
        $response = $this->_webApiCall($serviceInfo);
        $this->testCaseId = 1;
        $message = sprintf('Failed at Testcase  "%s" ', $this->testCaseId);

        $expectedItemsData = [
            [
                'name' => 'Primary Location',
                'qty' => 130,
                'is_current_location' => "1",
                'is_in_stock' => "1",
                'manage_stock' => "1",
                'use_config_manage_stock' => "1",
                'min_qty' => 0,
            ],
        ];

        /* check response is not empty */
        self::assertNotEmpty($response , $message);

        /* chek response has 1 item */
        /* change expected total count  = 3, because Shark team has create news location in SampleData */
        $expectedTotalCount = 3;
        self::assertCount($expectedTotalCount, $response , $message);

        /* check response is contains excepted data items */
        AssertArrayContains::assert($expectedItemsData, $response);
    }

    /**
     * Test case 2
     */
    public function testCase2(){
        include __DIR__ . '/../../_files/external_stock_data.php';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH
                    .'/'.$this->productId.'?pos_session=' . $this->posSession,
                'httpMethod' => RestRequest::HTTP_METHOD_GET,
            ]
        ];
        $response = $this->_webApiCall($serviceInfo);
        $this->testCaseId = 2;
        $message = sprintf('Failed at Testcase  "%s" ', $this->testCaseId);

        $expectedItemsData = [
            [
                'name' => 'Primary Location',
                'qty' => 130,
                'is_current_location' => "1",
                'is_in_stock' => "1",
                'manage_stock' => "1",
                'use_config_manage_stock' => "1",
                'min_qty' => 0,
            ],

            /* Data from Sample Data */
            [
                'min_qty' => 0,
            ],
            [
                'min_qty' => 0,
            ],
            /* done from Sample Data */

            [
                'name' => 'Location Test',
                'address' => '6146 Honey Bluff Parkway, Calder, United States, 49628-7978',
                'qty' => 100,
                'is_current_location' => "0",
                'is_in_stock' => "1",
                'manage_stock' => "1",
                'use_config_manage_stock' => "1",
                'min_qty' => 0,
            ],
        ];

        /* check response is not empty */
        self::assertNotEmpty($response , $message);

        /* chek response has 2 item */
        /* change expected total count  = 4, because Shark team has create news location in SampleData */
        $expectedTotalCount = 4;
        self::assertCount($expectedTotalCount, $response , $message);

        /* check response is contains excepted data items */
        AssertArrayContains::assert($expectedItemsData, $response);
        include __DIR__ . '/../../_files/external_stock_data_rollback.php';
    }


    /**
     * Test Case 3 try with product_id 99999999
     */
    public function testCase3(){
        $product_id = 99999999;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH
                    .'/'.$product_id.'?pos_session=' . $this->posSession,
                'httpMethod' => RestRequest::HTTP_METHOD_GET,
            ]
        ];
        $this->testCaseId = 3;
        $expectedMessage = sprintf('Product does not exist!');
        try {
            $this->_webApiCall($serviceInfo);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);
            /* check error message */
            $message = $this->getMessage($this->messageTxt);
            self::assertEquals($expectedMessage, $errorData['message'] , $message);
            /* check error code */
            $message = $this->getMessage($this->statusCodeTxt);
            self::assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode() , $message);
        }
    }


    /**
     * Test Case 4 - the pos_session is not valid
     */
    public function testCase4()
    {
        $this->testCaseId = 4;
        $pos_session = 'hca98erfksahrfkj3hy4r89ashfkhsdf98';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH
                    .'/'.$this->productId.'?pos_session=' . $pos_session,
                'httpMethod' => RestRequest::HTTP_METHOD_GET,
            ]
        ];
        $expectedMessage = sprintf('Session with id "%s" does not exist.', $pos_session);
        try {
            $this->_webApiCall($serviceInfo);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);

            /* check error message */
            $message = $this->getMessage($this->messageTxt);
            self::assertEquals($expectedMessage, $errorData['message'] , $message);
            /* check error code */
            $message = $this->getMessage($this->statusCodeTxt);
            self::assertEquals(Exception::HTTP_UNAUTHORIZED, $e->getCode() , $message);
        }
    }

    /**
     * Test Case 5 - the pos_session is missing
     */
    public function testCase5()
    {
        $this->testCaseId = 5;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH
                    .'/'.$this->productId,
                'httpMethod' => RestRequest::HTTP_METHOD_GET,
            ]
        ];
        $expectedMessage = 'Session with id "" does not exist.';
        try {
            $this->_webApiCall($serviceInfo);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);
            /* check error message */
            $message = $this->getMessage($this->messageTxt);
            self::assertEquals($expectedMessage, $errorData['message'], $message);
            /* check error code */
            $message = $this->getMessage($this->statusCodeTxt);
            self::assertEquals(Exception::HTTP_UNAUTHORIZED, $e->getCode() , $message);
        }
    }
}
