<?php

namespace Laconica\Analytics\Block\Html;

use Magento\Framework\View\Element\Template;

class Data extends Gtm
{
    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     */
    protected $groupRepository;

    /**
     * @var \Magento\Checkout\Model\Session $checkoutSession
     */
    protected $checkoutSession;

    /**
     * @var \Laconica\Analytics\Helper\Catalog $catalogHelper
     */
    protected $catalogHelper;

    /**
     * Data constructor.
     * @param Template\Context $context
     * @param \Laconica\Analytics\Helper\Config $configHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Laconica\Analytics\Helper\Catalog $catalogHelper
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Laconica\Analytics\Helper\Config $configHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Laconica\Analytics\Helper\Catalog $catalogHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    )
    {
        parent::__construct($context, $configHelper, $data);
        $this->configHelper = $configHelper;
        $this->customerSession = $customerSession;
        $this->groupRepository = $groupRepository;
        $this->checkoutSession = $checkoutSession;
        $this->catalogHelper = $catalogHelper;
    }


    /**
     * @return false|string
     */
    public function getPushJson()
    {
        $moduleName = $this->_request->getModuleName();
        $controller = $this->_request->getControllerName();
        $controller = ($controller === "noroute") ? "index" : $controller;
        $action = $this->_request->getActionName();
        $data = [
            "pageType" => implode("/", [$moduleName, $controller, $action])
        ];
        $customerInformation = $this->getCustomerInformation();
        $data = array_merge($data, $customerInformation);
        $categoryInformation = $this->getCategoryInformation();
        $data = array_merge($data, $categoryInformation);
        $productInformation = $this->getProductInformation();
        $data = array_merge($data, $productInformation);
        $cartInformation = $this->getTransactionInformation($data['pageType']);
        $data = array_merge($data, $cartInformation);

        return json_encode($data);
    }

    /**
     * @return array
     */
    protected function getCustomerInformation()
    {
        try {
            $groupEntity = $this->groupRepository->getById($this->customerSession->getCustomerGroupId());
            $groupCode = $groupEntity->getCode();
        } catch (\Exception $e) {
            $groupCode = "";
        }
        $customerInformation = [
            "customerLoggedIn" => intval($this->customerSession->isLoggedIn()),
            "customerId" => intval($this->customerSession->getCustomerId()),
            "customerGroupId" => intval($this->customerSession->getCustomerGroupId()),
            "customerGroupCode" => mb_strtoupper($groupCode)
        ];
        return $customerInformation;
    }

    /**
     * @return array
     */
    protected function getCategoryInformation()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->catalogHelper->getCurrentCategory();
        $productListBlock = $this->_layout->getBlock('category.products.list');
        $impressions = [];

        if (!$category || !$productListBlock) {
            return $impressions;
        }

        $categoryProducts = $productListBlock->getLoadedProductCollection();

        if (!$categoryProducts || $categoryProducts->getSize() <= 0) {
            return $impressions;
        }

        $productPosition = $category->getProductsPosition();
        $counter = 0;

        foreach ($categoryProducts as $product) {
            if ($counter > $this->configHelper->getExpressionsLimit()) {
                break;
            }
            if (!$product || !$product->getId()) {
                continue;
            }
            $productCategory = $product->getCategory();
            array_push($impressions, [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'price' => $this->configHelper->formatPrice($product->getFinalPrice()),
                'category' => ($productCategory) ? $productCategory->getName() : '',
                'position' => $counter // on old site like this
            ]);
            $counter++;
        }

        return [
            'categoryId' => $category->getId(),
            'categoryName' => $category->getName(),
            'categorySize' => count($productPosition),
            'categoryProducts' => $impressions
        ];
    }

    /**
     * @return array
     */
    protected function getProductInformation()
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->catalogHelper->getCurrentProduct();
        if (!$product) {
            return [];
        }
        $productCategory = $product->getCategory();
        $data = [
            'productId' => $product->getId(),
            'productName' => $product->getName(),
            'productSku' => $product->getSku(),
            'productPrice' => $this->configHelper->formatPrice($product->getFinalPrice()),
            'categoryId' => ($productCategory) ? $productCategory->getId() : 0,
            'categoryName' => ($productCategory) ? $productCategory->getName() : ''
        ];
        return $data;
    }

    /**
     * @param null $pageType
     * @return array
     */
    protected function getTransactionInformation($pageType = null)
    {
        $currentQuote = $this->checkoutSession->getQuote();
        $order = $this->checkoutSession->getLastRealOrder();
        $transactionInformation = [];

        if ($order && $order->getId()) {
            $payment = $order->getPayment();
            if (!$payment) {
                return $transactionInformation;
            }
            $method = $payment->getMethodInstance();
            $methodTitle = ($method) ? $method->getTitle() : "";
            $transactionInformation = [
                'transactionEntity' => 'ORDER',
                'transactionId' => $order->getIncrementId(),
                'transactionDate' => $order->getCreatedAt(),
                'transactionAffiliation' => $this->configHelper->getTransactionAffiliation(),
                'transactionTotal' => $this->configHelper->formatPrice($order->getGrandTotal()),
                'transactionSubtotal' => $this->configHelper->formatPrice($order->getSubtotal()),
                'transactionTax' => $this->configHelper->formatPrice($order->getTaxAmount()),
                'transactionShipping' => $this->configHelper->formatPrice($order->getShippingAmount()),
                'transactionPayment' => $methodTitle,
                'transactionCurrency' => $order->getOrderCurrencyCode(),
                'transactionPromoCode' => $order->getCouponCode(),
                'transactionProducts' => $this->getQuoteProducts($order->getAllItems(), true)
            ];
            return $transactionInformation;
        }

        if (in_array($pageType, $this->configHelper->getQuotePages()) && $currentQuote && $currentQuote->getId()) {
            $taxAmount = ($currentQuote->getShippingAddress()) ? $currentQuote->getShippingAddress()->getBaseTaxAmount() : 0;
            $transactionInformation = [
                'transactionEntity' => 'QUOTE',
                'transactionId' => $currentQuote->getId(),
                'transactionAffiliation' => $this->configHelper->getTransactionAffiliation(),
                'transactionTotal' => $this->configHelper->formatPrice($currentQuote->getGrandTotal()),
                'transactionTax' => $this->configHelper->formatPrice($taxAmount),
                'transactionProducts' => $this->getQuoteProducts($currentQuote->getAllItems())
            ];
            return $transactionInformation;
        }

        return $transactionInformation;
    }

    /**
     * @param $items
     * @param bool $isOrder
     * @return array
     */
    private function getQuoteProducts($items, $isOrder = false)
    {
        $products = [];
        if (!$items || !is_array($items)) {
            return $products;
        }
        foreach ($items as $item) {
            if ($item->getProductType() !== 'simple') {
                continue;
            }
            $qty = ($isOrder) ? $item->getQtyOrdered() : $item->getQty();
            $product = [
                'sku' => $item->getSku(),
                'name' => $item->getName(),
                'price' => $this->configHelper->formatPrice($item->getProduct()->getFinalPrice()),
                'quantity' => intval($qty)
            ];
            if ($isOrder) {
                $productCategories = $item->getProduct()->getCategoryIds();
                $product['category'] = (!empty($productCategories) && is_array($productCategories)) ? array_shift($productCategories) : 0;
            }
            array_push($products, $product);
        }
        return $products;
    }
}