<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option;

use Magestore\SupplierSuccess\Api\Data\SupplierInterface;
/**
 * Class SupplierEnable
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option
 */
class SupplierEnable extends \Magestore\PurchaseOrderSuccess\Model\Option\AbstractOption
{
    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory
     */
    protected $supplierCollectionFactory;

    /**
     * Supplier constructor.
     * @param \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory $supplierCollectionFactory
     */
    public function __construct(
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory $supplierCollectionFactory
    ){
        $this->supplierCollectionFactory = $supplierCollectionFactory;
    }
    
    public function getSupplierOptions(){
        $collection = $this->supplierCollectionFactory->create()
            ->addFieldToSelect(SupplierInterface::SUPPLIER_CODE)
            ->addFieldToSelect(SupplierInterface::SUPPLIER_NAME)
            ->addFieldToSelect('supplier_id')
            ->addFieldToFilter(SupplierInterface::STATUS, \Magestore\SupplierSuccess\Service\SupplierService::STATUS_ENABLE);
        $options = [' ' => __('Please select a supplier')];
        foreach ($collection->getItems() as $supplier){
            $options[$supplier->getId()] = $supplier->getSupplierName();
        }
        return $options;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionHash()
    {
        return $this->getSupplierOptions();
    }
}