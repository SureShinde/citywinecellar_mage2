<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail;

use Magento\Sales\Model\ResourceModel\Order\Item\Collection;

class Items extends \Magento\Sales\Block\Adminhtml\Order\View\Items
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $itemsCollection;

    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * Items constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemsCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemsCollection,
        \Magento\Shipping\Model\CarrierFactory $_carrierFactory,
        array $data = [])
    {
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $data);
        $this->itemsCollection = $itemsCollection;
        $this->_carrierFactory = $_carrierFactory;
    }

    /**
     * Retrieve order items collection
     *
     * @return Collection
     */
    public function getItemsCollection()
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $itemCollection */
        $itemCollection = $this->itemsCollection->create();
        $itemCollection->getSelect()
            ->join(
                ['packrequest_item' => $itemCollection->getTable('os_fulfilsuccess_packrequest_item')],
                'main_table.item_id = packrequest_item.item_id',
                ['*']
            )
            ->where("packrequest_item.request_qty > packrequest_item.packed_qty");

        $itemCollection
            ->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'order_id',
                $this->getOrder()->getId()
            )->load();

        return $itemCollection;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkCarrierAvailable()
    {

        $shippingCarrier = $this->_carrierFactory->create(
            $this->getOrder()->getShippingMethod(true)->getCarrierCode()
        );
        if ($shippingCarrier && $shippingCarrier->isShippingLabelsAvailable())
            return true;
        return false;
    }
}