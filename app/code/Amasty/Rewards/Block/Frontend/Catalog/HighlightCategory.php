<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Frontend\Catalog;

class HighlightCategory extends HighlightProduct
{
    /**
     * API path
     *
     * @var string
     */
    protected $path = 'rewards/mine/highlight/category';

    /**
     * @var \Amasty\Rewards\Model\Config
     */
    private $config;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Amasty\Rewards\Api\GuestHighlightManagementInterface $guestHighlightManagement,
        \Amasty\Rewards\Model\Config $config,
        array $data = []
    ) {
        parent::__construct($context, $sessionFactory, $guestHighlightManagement, $data);
        $this->config = $config;
    }

    /**
     * @return false|string
     */
    public function getJsLayout()
    {
        //Reset current data to prevent reloading components for processed products
        $this->jsLayout['components'] = null;

        $result = [
            'component' => 'Amasty_Rewards/js/model/catalog/highlight-category',
            'productId' => $this->getProductId(),
            'refreshUrl' => $this->getRefreshUrl(),
            'formSelector' => '[data-product-sku="' . $this->getProductSku() . '"]'
        ];

        $this->jsLayout['components']['amasty-rewards-highlight-catalog-' . $this->getProductId()] = $result;

        return json_encode($this->jsLayout);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->isVisible()) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return $this->isLoggedIn() && $this->config->getHighlightCategoryVisibility($this->_storeManager->getStore());
    }

    /**
     * @param int $productId
     *
     * @return HighlightCategory
     */
    public function setProductId($productId)
    {
        return $this->setData('product_id', $productId);
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->_getData('product_id');
    }

    /**
     * @param string $sku
     *
     * @return HighlightCategory
     */
    public function setProductSku($sku)
    {
        return $this->setData('product_sku', $sku);
    }

    /**
     * @return string
     */
    public function getProductSku()
    {
        return $this->_getData('product_sku');
    }
}
