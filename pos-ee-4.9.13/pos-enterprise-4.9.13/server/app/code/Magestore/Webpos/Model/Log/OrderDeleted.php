<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Log;
/**
 * Class OrderDeleted
 * @package Magestore\Webpos\Model\Log
 */
class OrderDeleted extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Log\OrderDeletedInterface
{
    /**
     * OrderDeleted constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Webpos\Model\ResourceModel\Log\OrderDeleted $resource
     * @param \Magestore\Webpos\Model\ResourceModel\Log\OrderDeleted\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Model\ResourceModel\Log\OrderDeleted $resource,
        \Magestore\Webpos\Model\ResourceModel\Log\OrderDeleted\Collection $resourceCollection,
        array $data = []
    ){
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get Id
     *
     * @return int
     */
    public function getId(){
        return $this->getData(self::ID);
    }
    /**
     * Set Id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id){
        return $this->setData(self::ID, $id);
    }

    /**
     * Get order id
     *
     * @return int
     */
    public function getOrderId(){
        return $this->getData(self::ORDER_ID);
    }
    /**
     * Set order id
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId){
        return $this->setData(self::ORDER_ID, $orderId);
    }
    /**
     * Get deleted at
     *
     * @return string
     */
    public function getDeletedAt(){
        return $this->getData(self::DELETED_AT);
    }
    /**
     * Set deleted at
     *
     * @param string $deletedAt
     * @return $this
     */
    public function setDeletedAt($deletedAt){
        return $this->setData(self::DELETED_AT, $deletedAt);
    }
}
