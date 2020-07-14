<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Source\Adminhtml;

/**
 * Class PurchaseOrder
 * @package Magestore\ReportSuccess\Model\Source\Adminhtml
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
     * Supplier constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager
    ){
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        if ($this->moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')) {
            $collection = $this->objectManager->create('Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Collection');
            $options[] = array('value' => ' ', 'label' => __('All Purchase Orders'));
            foreach ($collection as $item) {
                $options[] = array('value' => $item->getData('purchase_order_id'), 'label' => $item->getData('purchase_code'));
            }
        }
        return $options;
    }

    /**
     * @return array
     */
    public function toOptionListArray()
    {
        $options = [];
        if ($this->moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')) {
            $collection = $this->objectManager->create('Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Collection');
            foreach ($collection as $item) {
                $options[$item->getData('purchase_order_id')] = $item->getData('purchase_code');
            }
        }
        return $options;
    }

}
