<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\PackRequest;

use Magento\Framework\Model\AbstractModel;
use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;
use Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface;


class PackRequest extends AbstractModel implements PackRequestInterface
{
    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem\CollectionFactory
     */
    protected $packRequestItemCollectionFactory;

    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\CollectionFactory
     */
    protected $packageCollectionFactory;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem\CollectionFactory $packRequestItemCollectionFactory,
        \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\CollectionFactory $packageCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->packRequestItemCollectionFactory = $packRequestItemCollectionFactory;
        $this->packageCollectionFactory = $packageCollectionFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequest');
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
    public function getPickRequestId()
    {
        return $this->getData(self::PICK_REQUEST_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPickRequestId($pickRequestId)
    {
        return $this->setData(self::PICK_REQUEST_ID, $pickRequestId);
    }

    /**
     * @inheritDoc
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function getOrderIncrementId()
    {
        return $this->getData(self::ORDER_INCREMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderIncrementId($orderIncrementId)
    {
        return $this->setData(self::ORDER_INCREMENT_ID, $orderIncrementId);
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
    public function getAge()
    {
        return $this->getData(self::AGE);
    }

    /**
     * @inheritDoc
     */
    public function setAge($age)
    {
        return $this->setData(self::AGE, $age);
    }

    /**
     * @inheritDoc
     */
    public function getTotalItems()
    {
        return $this->getData(self::TOTAL_ITEMS);
    }

    /**
     * @inheritDoc
     */
    public function setTotalItems($totalItems)
    {
        return $this->setData(self::TOTAL_ITEMS, $totalItems);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }


    /**
     * @inheritDoc
     */
    public function getItems()
    {
        if ($this->getData(self::ITEMS) === null) {
            $collection = $this->packRequestItemCollectionFactory->create()
                ->addFieldToFilter(PackRequestItemInterface::PACK_REQUEST_ID, $this->getPackRequestId());
            if ($this->getPackRequestId()) {
                $this->setData(self::ITEMS, $collection->getItems());
            }
        }

        return $this->getData(self::ITEMS);
    }

    /**
     * @inheritDoc
     */
    public function getPackages()
    {
        if ($this->getData(PackRequestInterface::PACKAGES) === null) {
            $collection = $this->packageCollectionFactory->create()
                ->addFieldToFilter(self::PACK_REQUEST_ID, $this->getPackRequestId());
            if ($this->getPackRequestId()) {
                $this->setData(PackRequestInterface::PACKAGES, $collection->getItems());
            }
        }

        return $this->getData(PackRequestInterface::PACKAGES);
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