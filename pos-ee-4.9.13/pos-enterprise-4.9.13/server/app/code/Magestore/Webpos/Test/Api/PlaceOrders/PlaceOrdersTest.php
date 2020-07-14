<?php

namespace Magestore\Webpos\Test\Api\PlaceOrders;

use Magento\Framework\Api\SearchCriteria;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Exception;
use Magento\TestFramework\Helper\Bootstrap;

use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magestore\Webpos\Test\Api\GetSessionTrait;
use Magestore\Webpos\Test\Api\PlaceOrders\PlaceOrderTrait;
use Magento\Catalog\Api\ProductRepositoryInterface;

use Magestore\Webpos\Test\Constant\Product;

class PlaceOrdersTest extends WebapiAbstract
{

    use GetSessionTrait;
    use PlaceOrderTrait;

    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/webpos/checkout/placeOrder';
    const SERVICE_NAME = 'placeOrderRepositoryV1';

    protected $posSession;
    protected $timeZone;
    protected $apiName = "placeOrder";

    /**
     * @var
     */
    protected $productRepository;

    protected function setUp()
    {
        $this->posSession = $this->loginAndAssignPos();
        $this->timeZone = Bootstrap::getObjectManager()->get('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $this->productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
    }

    /**
     * Test Case 1
     */
    public function testCase1(){
        $currentTimeStamp = $this->timeZone->scopeTimeStamp();
        $itemDataFactory = Bootstrap::getObjectManager()->get('\Magestore\Webpos\Test\Api\PlaceOrders\RequestData\ItemsData');
        $requestOrderData = Bootstrap::getObjectManager()->get('\Magestore\Webpos\Test\Api\PlaceOrders\RequestData\OrderData');

        $productSku = Product::SKU_13;
        $productName = Product::NAME_13;
        $product = $this->productRepository->get($productSku);
        $productId = $product->getId();
        $qtyOrdered = 1;

        $ship = 1;
        $invoice = 1;
        $paymentMethod = 'cashforpos';

        $items = $itemDataFactory->getSchemaJson($currentTimeStamp, $productSku, $productName, $productId, $qtyOrdered);
        $requestData = $requestOrderData->getSchemaJson($ship, $invoice, $currentTimeStamp, $paymentMethod, $items);
        $requestData = json_decode($requestData, true);
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH
                    .'?pos_session=' . $this->posSession,
                'httpMethod' => RestRequest::HTTP_METHOD_POST,
            ]
        ];
        return true;
        $reponse = $this->_webApiCall($serviceInfo, $requestData);
    }
}
