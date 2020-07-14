<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Block\Banner;

use Magento\Framework\View\Element\Template;

class ProductListing extends Template
{
    protected $_template = 'product_listing.phtml';

    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    private $listProduct;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;

    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Block\Product\ListProduct $listProduct,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->listProduct = $listProduct;
        $this->formKey = $formKey;
    }

    public function getProductListingBlock()
    {
        return $this->listProduct;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
