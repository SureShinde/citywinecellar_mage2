<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api\Data;

/**
 * Interface PackageInterface
 * @package Magestore\FulfilSuccess\Api\Data
 */
interface PackageInterface
{
    CONST PACKAGE_ID = 'package_id';
    CONST PACK_REQUEST_ID = 'pack_request_id';
    CONST SHIPMENT_ID = 'shipment_id';
    CONST TRACK_ID = 'track_id';
    CONST WAREHOUSE_ID = 'warehouse_id';
    CONST CONTAINER = 'container';
    CONST WEIGHT = 'weight';
    CONST CUSTOM_VALUE = 'custom_value';
    CONST LENGTH = 'length';
    CONST WIDTH = 'width';
    CONST HEIGHT = 'height';
    CONST WEIGHT_UNITS = 'weight_units';
    CONST DIMENSION_UNITS = 'dimension_units';
    CONST CONTENT_TYPE = 'content_type';
    CONST CONTENT_TYPE_OTHER = 'content_type_other';
    CONST DELIVERY_CONFIRMATION = 'delivery_confirmation';
    CONST IMAGE = 'image';
    CONST ITEMS = 'items';
    const SOURCE_CODE = 'source_code';

    /**
     * @return int
     */
    public function getPackageId();

    /**
     * @param $packageId
     * @return $this
     */
    public function setPackageId($packageId);

    /**
     * @return int
     */
    public function getPackRequestId();

    /**
     * @param int $packRequestId
     * @return $this
     */
    public function setPackRequestId($packRequestId);

    /**
     * @return int
     */
    public function getShipmentId();

    /**
     * @param int $shipmentId
     * @return $this
     */
    public function setShipmentId($shipmentId);

    /**
     * @return int
     */
    public function getTrackId();

    /**
     * @param $trackId
     * @return $this
     */
    public function setTrackId($trackId);

    /**
     * @return int
     */
    public function getWarehouseId();

    /**
     * @param int $warehouseId
     * @return $this
     */
    public function setWarehouseId($warehouseId);

    /**
     * @return string
     */
    public function getContainer();

    /**
     * @param $container
     * @return $this
     */
    public function setContainer($container);

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @param $weight
     * @return $this
     */
    public function setWeight($weight);

    /**
     * @return string
     */
    public function getCustomValue();

    /**
     * @param $customValue
     * @return $this
     */
    public function setCustomValue($customValue);

    /**
     * @return float
     */
    public function getLength();

    /**
     * @param $length
     * @return $this
     */
    public function setLength($length);

    /**
     * @return float
     */
    public function getWidth();

    /**
     * @param $width
     * @return $this
     */
    public function setWidth($width);

    /**
     * @return float
     */
    public function getHeight();

    /**
     * @param $height
     * @return $this
     */
    public function setHeight($height);

    /**
     * @return string
     */
    public function getWeightUnits();

    /**
     * @param $weightUnits
     * @return $this
     */
    public function setWeightUnits($weightUnits);

    /**
     * @return string
     */
    public function getDimensionUnits();

    /**
     * @param $dimensionUnits
     * @return $this
     */
    public function setDimensionUnits($dimensionUnits);

    /**
     * @return string
     */
    public function getContentType();

    /**
     * @param $contentType
     * @return $this
     */
    public function setContentType($contentType);

    /**
     * @return string
     */
    public function getContentTypeOther();

    /**
     * @param $contentTypeOther
     * @return $this
     */
    public function setContentTypeOther($contentTypeOther);

    /**
     * @return string
     */
    public function getDeliveryConfirmation();

    /**
     * @param $deliveryConfirmation
     * @return $this
     */
    public function setDeliveryConfirmation($deliveryConfirmation);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param $image
     * @return $this
     */
    public function setImage($image);

    /**
     * @return \Magestore\FulfilSuccess\Model\ResourceModel\Package\PackageItem\Collection
     */
    public function getItems();

    /**
     * get updated at
     *
     * @return string
     */
    public function getSourceCode();

    /**
     * set Source Code
     *
     * @param string $sourceCode
     */
    public function setSourceCode($sourceCode);
}