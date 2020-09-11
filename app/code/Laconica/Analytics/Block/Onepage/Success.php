<?php

namespace Laconica\Analytics\Block\Onepage;

use Magento\Checkout\Block\Onepage\Success as ParentSuccess;

class Success extends ParentSuccess
{
    /**
     * @var \Laconica\Analytics\Helper\Config $configHelper
     */
    protected $configHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Laconica\Analytics\Helper\Config $configHelper,
        array $data = []
    )
    {
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
        $this->configHelper = $configHelper;
    }

    public function getConfigHelper()
    {
        return $this->configHelper;
    }

    public function getOnepageSuccessJson()
    {
        try {
            $products = [];
            $order = $this->_checkoutSession->getLastRealOrder();
            if (!$order) {
                return $products;
            }
            $orderProducts = $order->getAllItems();
            foreach ($orderProducts as $product) {
                if ($product->getProductType() !== 'simple') {
                    continue;
                }
                array_push($products, [
                    'name' => $product->getName(),
                    'id' => $product->getId(),
                    'price' => $this->configHelper->formatPrice($product->getProduct()->getPrice()),
                    'size' => (string)$product->getProduct()->getAttributeText('size'),
                    'color' => (string)$product->getProduct()->getAttributeText('color'),
                    'quantity' => intval($product->getQtyOrdered())
                ]);
            }
            $dataLayer = [
                'event' => 'orderSuccess',
                'ecommerce' => [
                    'purchase' => [
                        'actionField' => [
                            'id' => $order->getIncrementId(),
                            'affiliation' => 'Online Store',
                            'revenue' => $this->configHelper->formatPrice($order->getGrandTotal()), // Total transaction value (incl. tax and shipping)
                            'tax' => $this->configHelper->formatPrice($order->getTaxAmount()),
                            'shipping' => $this->configHelper->formatPrice($order->getShippingAmount()),
                            'coupon' => $this->configHelper->formatPrice($order->getDiscountAmount()),
                            'giftcard' => $this->configHelper->formatPrice($order->getGiftVoucherDiscount())
                        ]
                    ],
                    'products' => $products
                ]
            ];
            return json_encode($dataLayer);
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage(), $e->getTrace());
            return false;
        }
    }

}
