<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Source\Adminhtml;

/**
 * Class Supplier
 * @package Magestore\ReportSuccess\Model\Source\Adminhtml
 */
class Supplier implements \Magento\Framework\Option\ArrayInterface
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
        if ($this->moduleManager->isEnabled('Magestore_SupplierSuccess')) {
            $collection = $this->objectManager->create('Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection');
            $options[] = array('value' => ' ', 'label' => __('All Suppliers'));
            foreach ($collection as $item) {
                $options[] = array('value' => $item->getSupplierId(), 'label' => $item->getSupplierName());
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
        if ($this->moduleManager->isEnabled('Magestore_SupplierSuccess')) {
            $collection = $this->objectManager->create('Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection');
            foreach ($collection as $item) {
                $options[$item->getSupplierId()] = $item->getSupplierName();
            }
        }
        return $options;
    }

}
