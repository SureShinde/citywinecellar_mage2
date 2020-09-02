<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api\Data;


interface DropshipSupplierShipmentInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const SUPPLIER_SHIPMENT_ID = 'supplier_shipment_id';
    const SUPPLIER_ID = 'supplier_id';
    const SHIPMENT_ID = 'shipment_id';
    const SUPPLIER_CODE = 'supplier_code';
    const SUPPLIER_NAME = 'supplier_name';

    /**#@-*/

    /**
     * Dropship Supplier Shipment id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Dropship Supplier Shipment id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);


    /**
     * Supplier Id
     *
     * @return int|null
     */
    public function getSupplierId();

    /**
     * Set supplier id
     *
     * @param string $supplierId
     * @return $this
     */
    public function setSupplierId($supplierId);

    /**
     * Shipment Id
     *
     * @return int|null
     */
    public function getShipmentId();

    /**
     * Set shipment id
     *
     * @param string $shipmentId
     * @return $this
     */
    public function setShipmentId($shipmentId);

    /**
     * Supplier Code
     *
     * @return string|null
     */
    public function getSupplierCode();

    /**
     * Set Supplier Code
     *
     * @param string $supplierCode
     * @return $this
     */
    public function setSupplierCode($supplierCode);

    /**
     * Supplier Name
     *
     * @return string|null
     */
    public function getSupplierName();

    /**
     * Set Supplier Name
     *
     * @param string $supplierName
     * @return $this
     */
    public function setSupplierName($supplierName);

}