<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Model\DropshipRequest;

/**
 * Class DropshipShipment
 * @package Magestore\DropshipSuccess\Model\DropshipRequest
 */
class DropshipShipment extends \Magento\Framework\Model\AbstractModel
    implements \Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface
{

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment');
    }

    /**
     * Dropship shipment id
     *
     * @return int|null
     */
    public function getDropshipShipmentId(){
        return $this->_getData(self::DROPSHIP_SHIPMENT_ID);
    }

    /**
     * Set dropship shipment id
     *
     * @param int $dropshipShipmentId
     * @return $this
     */
    public function setDropshipShipmentId($dropshipShipmentId){
        return $this->setData(self::DROPSHIP_SHIPMENT_ID, $dropshipShipmentId);
    }

    /**
     * Dropship request id
     *
     * @return int|null
     */
    public function getDropshipRequestId(){
        return $this->_getData(self::DROPSHIP_REQUEST_ID);
    }

    /**
     * Set dropship request id
     *
     * @param int $dropshipRequestId
     * @return $this
     */
    public function setDropshipRequestId($dropshipRequestId){
        return $this->setData(self::DROPSHIP_REQUEST_ID, $dropshipRequestId);
    }

    /**
     * Shipment Id
     *
     * @return string|null
     */
    public function getShipmentId(){
        return $this->_getData(self::SHIPMENT_ID);
    }

    /**
     * Set shipment id
     *
     * @param string $shipmentId
     * @return $this
     */
    public function setShipmentId($shipmentId){
        return $this->setData(self::SHIPMENT_ID, $shipmentId);
    }

    /**
     * Carrier code
     *
     * @return string|null
     */
    public function getCarrierCode(){
        return $this->_getData(self::CARRIER_CODE);
    }

    /**
     * Set carrier code
     *
     * @param string $carrierCode
     * @return $this
     */
    public function setCarrierCode($carrierCode){
        return $this->setData(self::CARRIER_CODE, $carrierCode);
    }

    /**
     * Shipping label
     *
     * @return string|null
     */
    public function getShippingLabel(){
        return $this->_getData(self::SHIPPING_LABEL);
    }

    /**
     * Set shipping label
     *
     * @param string $shippingLabel
     * @return $this
     */
    public function setShippingLabel($shippingLabel){
        return $this->setData(self::SHIPPING_LABEL, $shippingLabel);
    }

    /**
     * Track number
     *
     * @return string|null
     */
    public function getTrackNumber(){
        return $this->_getData(self::TRACK_NUMBER);
    }

    /**
     * Set track number
     *
     * @param string $trackNumber
     * @return $this
     */
    public function setTrackNumber($trackNumber){
        return $this->setData(self::TRACK_NUMBER, $trackNumber);
    }

    /**
     * Total shipped
     *
     * @return float|null
     */
    public function getTotalShipped(){
        return $this->_getData(self::TOTAL_SHIPPED);
    }

    /**
     * Set total shipped
     *
     * @param float $totalShipped
     * @return $this
     */
    public function setTotalShipped($totalShipped){
        return $this->setData(self::TOTAL_SHIPPED, $totalShipped);
    }

    /**
     * Created at
     *
     * @return string|null
     */
    public function getCreatedAt(){
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set Created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}