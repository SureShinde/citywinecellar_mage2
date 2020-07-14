<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Package;

use Magento\Framework\Model\AbstractModel;
use Magestore\FulfilSuccess\Api\Data\PackageInterface;

class Package extends AbstractModel implements PackageInterface
{
    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\Package\PackageItem\CollectionFactory
     */
    protected $packageItemCollectionFactory;

    /**
     * Package constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param \Magestore\FulfilSuccess\Model\ResourceModel\Package\PackageItem\CollectionFactory $packageItemCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\FulfilSuccess\Model\ResourceModel\Package\PackageItem\CollectionFactory $packageItemCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->packageItemCollectionFactory = $packageItemCollectionFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\FulfilSuccess\Model\ResourceModel\Package\Package');
    }

    /**
     * @inheritDoc
     */
    public function getPackageId()
    {
        return $this->getData(self::PACKAGE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPackageId($packageId)
    {
        return $this->setData(self::PACKAGE_ID, $packageId);
    }

    /**
     * @inheritDoc
     */
    public function getPackRequestId()
    {
        return $this->getData(self::PACK_REQUEST_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPackRequestId($packRequestId)
    {
        return $this->setData(self::PACK_REQUEST_ID, $packRequestId);
    }

    /**
     * @inheritDoc
     */
    public function getShipmentId()
    {
        return $this->getData(self::SHIPMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setShipmentId($shipmentId)
    {
        return $this->setData(self::SHIPMENT_ID, $shipmentId);
    }


    /**
     * @inheritDoc
     */
    public function getTrackId()
    {
        return $this->getData(self::TRACK_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTrackId($trackId)
    {
        return $this->setData(self::TRACK_ID, $trackId);
    }

    /**
     * @inheritDoc
     */
    public function getWarehouseId()
    {
        return $this->getData(self::WAREHOUSE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setWarehouseId($warehouseId)
    {
        return $this->setData(self::WAREHOUSE_ID, $warehouseId);
    }


    /**
     * @inheritDoc
     */
    public function getContainer()
    {
        return $this->getData(self::CONTAINER);
    }

    /**
     * @inheritDoc
     */
    public function setContainer($container)
    {
        return $this->setData(self::CONTAINER, $container);
    }

    /**
     * @inheritDoc
     */
    public function getWeight()
    {
        return $this->getData(self::WEIGHT);
    }

    /**
     * @inheritDoc
     */
    public function setWeight($weight)
    {
        return $this->setData(self::WEIGHT, $weight);
    }

    /**
     * @inheritDoc
     */
    public function getCustomValue()
    {
        return $this->getData(self::CUSTOM_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setCustomValue($customValue)
    {
        return $this->setData(self::CUSTOM_VALUE, $customValue);
    }

    /**
     * @inheritDoc
     */
    public function getLength()
    {
        return $this->getData(self::LENGTH);
    }

    /**
     * @inheritDoc
     */
    public function setLength($length)
    {
        return $this->setData(self::LENGTH, $length);
    }

    /**
     * @inheritDoc
     */
    public function getWidth()
    {
        return $this->getData(self::WIDTH);
    }

    /**
     * @inheritDoc
     */
    public function setWidth($width)
    {
        return $this->setData(self::WIDTH, $width);
    }

    /**
     * @inheritDoc
     */
    public function getHeight()
    {
        return $this->getData(self::HEIGHT);
    }

    /**
     * @inheritDoc
     */
    public function setHeight($height)
    {
        return $this->setData(self::HEIGHT, $height);
    }

    /**
     * @inheritDoc
     */
    public function getWeightUnits()
    {
        return $this->getData(self::WEIGHT_UNITS);
    }

    /**
     * @inheritDoc
     */
    public function setWeightUnits($weightUnits)
    {
        return $this->setData(self::WEIGHT_UNITS, $weightUnits);
    }

    /**
     * @inheritDoc
     */
    public function getDimensionUnits()
    {
        return $this->getData(self::DIMENSION_UNITS);
    }

    /**
     * @inheritDoc
     */
    public function setDimensionUnits($dimensionUnits)
    {
        return $this->setData(self::DIMENSION_UNITS, $dimensionUnits);
    }

    /**
     * @inheritDoc
     */
    public function getContentType()
    {
        return $this->getData(self::CONTENT_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setContentType($contentType)
    {
        return $this->setData(self::CONTENT_TYPE, $contentType);
    }

    /**
     * @inheritDoc
     */
    public function getContentTypeOther()
    {
        return $this->getData(self::CONTENT_TYPE_OTHER);
    }

    /**
     * @inheritDoc
     */
    public function setContentTypeOther($contentTypeOther)
    {
        return $this->setData(self::CONTENT_TYPE_OTHER, $contentTypeOther);
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryConfirmation()
    {
        return $this->getData(self::DELIVERY_CONFIRMATION);
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryConfirmation($deliveryConfirmation)
    {
        return $this->setData(self::DELIVERY_CONFIRMATION, $deliveryConfirmation);
    }

    /**
     * @inheritDoc
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        /** @var \Magestore\FulfilSuccess\Model\ResourceModel\Package\PackageItem\Collection $packageItemCollection */
        $packageItemCollection = $this->packageItemCollectionFactory->create();
        $packageItemCollection->addFieldToFilter(self::PACKAGE_ID, $this->getPackageId());

        return $packageItemCollection;
    }

    /**
     * get updated at
     *
     * @return string
     */
    public function getSourceCode()
    {
        return $this->getData(self::SOURCE_CODE);
    }

    /**
     * set Source Code
     *
     * @param string $sourceCode
     */
    public function setSourceCode($sourceCode)
    {
        return $this->setData(self::SOURCE_CODE, $sourceCode);
    }
}