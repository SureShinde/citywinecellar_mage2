<?php

namespace Laconica\Analytics\Block\Product;

use Magento\Framework\View\Element\Template;

class Event extends \Laconica\Analytics\Block\Html\Gtm
{
    /**
     * @var \Magento\Framework\Registry $registry
     */
    protected $registry;

    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    protected $customerSession;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Laconica\Analytics\Helper\Config $configHelper,
        \Magento\Customer\Model\Session $customerSession,
        array $data = [])
    {
        parent::__construct($context, $configHelper, $data);
        $this->registry = $registry;
        $this->customerSession = $customerSession;
    }

    public function getProductInfoJson()
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->registry->registry('current_product');
        if (!$product) {
            return false;
        }
        $data = [
            'sku' => $product->getSku(),
            'name' => $product->getName(),
            'price' => $this->configHelper->formatPrice($product->getFinalPrice()),
            'color' => (string)$product->getAttributeText('color'),
            'size' => (string)$product->getAttributeText('size')
        ];
        return json_encode($data);
    }

    public function isCustomerLoggedIn(){
        return $this->customerSession->isLoggedIn();
    }
}