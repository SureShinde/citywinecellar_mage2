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
class CustomerDeleted extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Log\CustomerDeletedInterface
{
    /**
     * OrderDeleted constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Webpos\Model\ResourceModel\Log\CustomerDeleted $resource
     * @param \Magestore\Webpos\Model\ResourceModel\Log\CustomerDeleted\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Model\ResourceModel\Log\CustomerDeleted $resource,
        \Magestore\Webpos\Model\ResourceModel\Log\CustomerDeleted\Collection $resourceCollection,
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
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId(){
        return $this->getData(self::CUSTOMER_ID);
    }
    /**
     * Set customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId){
        return $this->setData(self::CUSTOMER_ID, $customerId);
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
