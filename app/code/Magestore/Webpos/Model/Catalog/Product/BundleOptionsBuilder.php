<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Catalog\Product;

use Magento\Catalog\Model\ProductFactory;

/**
 * Product BundleOptionsBuilder
 */
class BundleOptionsBuilder
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var int|null
     */
    protected $productId = null;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_pricingHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Bundle\Model\Option
     */
    protected $_bundleOption;

    /**
     * @var \Magento\Bundle\Model\Product\Type
     */
    protected $_bundleType;

    /**
     * BundleOptionsBuilder constructor.
     *
     * @param ProductFactory $productFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Bundle\Model\Option $bundleOption
     * @param \Magento\Bundle\Model\Product\Type $bundleType
     */
    public function __construct(
        ProductFactory $productFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Bundle\Model\Option $bundleOption,
        \Magento\Bundle\Model\Product\Type $bundleType
    ) {
        $this->productFactory = $productFactory;
        $this->_objectManager = $objectManager;
        $this->_pricingHelper = $pricingHelper;
        $this->_storeManager = $storeManager;
        $this->_bundleOption = $bundleOption;
        $this->_bundleType = $bundleType;
    }

    /**
     * Set Product Id
     *
     * @param int $productId
     * @return void
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * GetProductId
     *
     * @return int|null
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Create
     *
     * @return BundleOptionsInterface[]|null
     */
    public function create()
    {
        if ($this->getProductId()) {
            $product = $this->productFactory->create()->load($this->getProductId());
            $bundleChilds = [];
            $store_id = $this->_storeManager->getStore()->getId();
            $options = $this->_bundleOption->getResourceCollection()
                ->setProductIdFilter($product->getId())
                ->setPositionOrder();
            $options->joinValues($store_id);
            $typeInstance = $this->_bundleType;
            $selections = $typeInstance->getSelectionsCollection($typeInstance->getOptionsIds($product), $product);
            $price_type = $product->getData('price_type');
            foreach ($options->getItems() as $option) {
                $bundleChilds[$option->getId()]['title'] = $option->getTitle();
                $bundleChilds[$option->getId()]['required'] = $option->getRequired();
                $bundleChilds[$option->getId()]['type'] = $option->getType();
                $bundleChilds[$option->getId()]['id'] = $option->getId();
                $bundleChilds[$option->getId()]['product_id'] = $product->getId();
                foreach ($selections as $selection) {
                    $selection_price_type = $selection->getData('selection_price_type');
                    $selection_price_value = $selection->getData('selection_price_value');
                    $price = $selection->getData('price');
                    $selection_price = ($selection_price_type == 0)
                        ? $selection_price_value
                        : $price * $selection_price_value;

                    if ($price_type == 0) {
                        $selection_price = $price;
                    }
                    if ($option->getId() == $selection->getOptionId()) {
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()] = [];
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()]['name']
                            = $selection->getName();
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()]['qty']
                            = $selection->getSelectionQty();
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()]['price']
                            = $selection_price;
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()]['can_change_qty']
                            = $selection->getSelectionCanChangeQty();
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()]['is_default']
                            = $selection->getIsDefault();
                    }
                }
            };
            return $bundleChilds;
        }
        return null;
    }

    /**
     * FormatPrice
     *
     * @param string $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->_pricingHelper->currency($price, true, false);
    }
}
