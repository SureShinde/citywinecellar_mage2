<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Source\Adminhtml;

/**
 * Purchase Order model
 */
class PurchaseOrder implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * PurchaseOrder constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = [];
        if ($this->moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')) {
            $collection = $this->objectManager->create(
                \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Collection::class
            );
            $options[] = ['value' => ' ', 'label' => __('All Purchase Orders')];
            foreach ($collection as $item) {
                $options[] = [
                    'value' => $item->getData('purchase_order_id'),
                    'label' => $item->getData('purchase_code')
                ];
            }
        }
        return $options;
    }

    /**
     * To Option List Array
     *
     * @return array
     */
    public function toOptionListArray()
    {
        $options = [];
        if ($this->moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')) {
            $collection = $this->objectManager->create(
                \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Collection::class
            );
            foreach ($collection as $item) {
                $options[$item->getData('purchase_order_id')] = $item->getData('purchase_code');
            }
        }
        return $options;
    }
}
