<?php

namespace Laconica\Catalog\Block\Product\View;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

class Attributes extends \Magento\Catalog\Block\Product\View\Attributes
{

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Attributes constructor.
     * @param Context $context
     * @param Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $priceCurrency, $data);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return Collection|AbstractDb|null
     */
    public function getRelatedProducts()
    {
        $productIds = $this->getProduct()->getCustomAttribute('sibling_product_skus');
        if ($productIds && $productIds->getValue()) {
            return $this->collectionFactory->create()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('sku', ['in' => $productIds->getValue()]);
        }
        return null;
    }
}
