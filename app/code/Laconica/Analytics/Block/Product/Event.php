<?php

namespace Laconica\Analytics\Block\Product;

use Magento\Framework\View\Element\Template;

class Event extends \Laconica\Analytics\Block\Html\Gtm
{
    /**
     * @var \Laconica\Analytics\Helper\Catalog $catalogHelper
     */
    protected $catalogHelper;

    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    protected $customerSession;

    /**
     * Event constructor.
     * @param Template\Context $context
     * @param \Laconica\Analytics\Helper\Catalog $catalogHelper
     * @param \Laconica\Analytics\Helper\Config $configHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Laconica\Analytics\Helper\Catalog $catalogHelper,
        \Laconica\Analytics\Helper\Config $configHelper,
        \Magento\Customer\Model\Session $customerSession,
        array $data = [])
    {
        parent::__construct($context, $configHelper, $data);
        $this->catalogHelper = $catalogHelper;
        $this->customerSession = $customerSession;
    }

    /**
     * @return bool|false|string
     */
    public function getProductInfoJson()
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->catalogHelper->getCurrentProduct();
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

    /**
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }
}