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
     * @var \Magento\Framework\Registry $registry
     */
    protected $registry;

    public function __construct(
        Template\Context $context,
        \Laconica\Analytics\Helper\Config $configHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,
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
        $this->registry = $registry;
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
    protected function getCategoryInformation(){
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->registry->registry('current_category');
        if (!$category) {
            return [];
        }
        $productListBlock = $this->_layout->getBlock('category.products.list');

        if (empty($productListBlock)) {
            return [];
        }

        $categoryProducts = $productListBlock->getLoadedProductCollection();
        $productPosition = $category->getProductsPosition();
        $impressions = [];
        $counter = 0;
        foreach ($categoryProducts as $product) {
            if($counter > $this->configHelper->getExpressionsLimit()){
                break;
            }
            if(!$product || !$product->getId()){
                continue;
            }
            $productCategory = $product->getCategory();
            array_push($impressions, [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'price' => $this->configHelper->formatPrice($product->getPrice()),
                'category' => ($productCategory) ? $productCategory->getName() : '',
                'position' => $counter
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
    protected function getProductInformation(){
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->registry->registry('current_product');
        if (!$product) {
            return [];
        }
        $productCategory = $product->getCategory();
        $data = [
            'productId' => $product->getId(),
            'productName' => $product->getName(),
            'productSku' => $product->getSku(),
            'productPrice' => $this->configHelper->formatPrice($product->getPrice()),
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
            $method = $payment->getMethodInstance();
            $methodTitle = ($method) ? $method->getTitle() : "";
            $transactionInformation = [
                'transactionEntity' => 'ORDER',
                'transactionId' => $order->getIncrementId(),
                'transactionDate' => $order->getCreatedAt(),
                'transactionAffiliation' => 'Main Website',
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
                'transactionAffiliation' => 'Main Website',
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
                'price' => $this->configHelper->formatPrice($item->getProduct()->getPrice()),
                'quantity' => intval($qty)
            ];
            if ($isOrder) {
                $productCategories = $item->getProduct()->getCategoryIds();
                $product['category'] = (!empty($productCategories)) ? array_shift($productCategories) : 0;
            }
            array_push($products, $product);
        }
        return $products;
    }
}