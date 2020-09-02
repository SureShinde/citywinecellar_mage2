<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\Supplier;

/**
 * Class Shipment
 * @package Magestore\DropshipSuccess\Model\Supplier
 */
class Shipment extends \Magento\Framework\Model\AbstractModel
    implements \Magestore\DropshipSuccess\Api\Data\DropshipSupplierShipmentInterface
{

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\DropshipSuccess\Model\ResourceModel\Supplier\Shipment');
    }

    /**
     * Dropship Supplier Shipment id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_getData(self::SUPPLIER_SHIPMENT_ID);
    }

    /**
     * Set Dropship Supplier Shipment id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::SUPPLIER_SHIPMENT_ID, $id);
    }


    /**
     * Supplier Id
     *
     * @return int|null
     */
    public function getSupplierId()
    {
        return $this->_getData(self::SUPPLIER_ID);
    }

    /**
     * Set supplier id
     *
     * @param string $supplierId
     * @return $this
     */
    public function setSupplierId($supplierId)
    {
        return $this->setData(self::SUPPLIER_ID, $supplierId);
    }

    /**
     * Shipment Id
     *
     * @return int|null
     */
    public function getShipmentId()
    {
        return $this->_getData(self::SHIPMENT_ID);
    }

    /**
     * Set shipment id
     *
     * @param string $shipmentId
     * @return $this
     */
    public function setShipmentId($shipmentId)
    {
        return $this->setData(self::SHIPMENT_ID, $shipmentId);
    }

    /**
     * Supplier Code
     *
     * @return string|null
     */
    public function getSupplierCode()
    {
        return $this->_getData(self::SUPPLIER_CODE);
    }

    /**
     * Set Supplier Code
     *
     * @param string $supplierCode
     * @return $this
     */
    public function setSupplierCode($supplierCode)
    {
        return $this->setData(self::SUPPLIER_CODE, $supplierCode);
    }

    /**
     * Supplier Name
     *
     * @return string|null
     */
    public function getSupplierName()
    {
        return $this->_getData(self::SUPPLIER_NAME);
    }

    /**
     * Set Supplier Name
     *
     * @param string $supplierName
     * @return $this
     */
    public function setSupplierName($supplierName)
    {
        return $this->setData(self::SUPPLIER_NAME, $supplierName);
    }
}