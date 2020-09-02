<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api\Data;

/**
 * Interface DropshipShipmentInterface
 * @package Magestore\DropshipSuccess\Api\Data
 */
interface DropshipShipmentInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const DROPSHIP_SHIPMENT_ID = 'dropship_shipment_id';
    const DROPSHIP_REQUEST_ID = 'dropship_request_id';
    const SHIPMENT_ID = 'shipment_id';
    const CARRIER_CODE = 'carrier_code';
    const SHIPPING_LABEL = 'shipping_label';
    const TRACK_NUMBER = 'track_number';
    const TOTAL_SHIPPED = 'total_shipped';
    const CREATED_AT = 'created_at';
    const CREATE_SHIPMENT_BY_DROPSHIP = 'create_shipment_by_dropship';


    /**#@-*/

    /**
     * Dropship shipment id
     *
     * @return int|null
     */
    public function getDropshipShipmentId();

    /**
     * Set dropship shipment id
     *
     * @param int $dropshipShipmentId
     * @return $this
     */
    public function setDropshipShipmentId($dropshipShipmentId);

    /**
     * Dropship request id
     *
     * @return int|null
     */
    public function getDropshipRequestId();

    /**
     * Set dropship request id
     *
     * @param int $dropshipRequestId
     * @return $this
     */
    public function setDropshipRequestId($dropshipRequestId);

    /**
     * Shipment Id
     *
     * @return string|null
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
     * Carrier code
     *
     * @return string|null
     */
    public function getCarrierCode();

    /**
     * Set carrier code
     *
     * @param string $carrierCode
     * @return $this
     */
    public function setCarrierCode($carrierCode);

    /**
     * Shipping label
     *
     * @return string|null
     */
    public function getShippingLabel();

    /**
     * Set shipping label
     *
     * @param string $shippingLabel
     * @return $this
     */
    public function setShippingLabel($shippingLabel);

    /**
     * Track number
     *
     * @return string|null
     */
    public function getTrackNumber();

    /**
     * Set track number
     *
     * @param string $trackNumber
     * @return $this
     */
    public function setTrackNumber($trackNumber);

    /**
     * Total shipped
     *
     * @return float|null
     */
    public function getTotalShipped();

    /**
     * Set total shipped
     *
     * @param float $totalShipped
     * @return $this
     */
    public function setTotalShipped($totalShipped);

    /**
     * Created at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set Created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);
}