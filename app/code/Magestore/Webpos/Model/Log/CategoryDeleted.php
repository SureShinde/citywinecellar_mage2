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
class CategoryDeleted extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterface
{
    /**
     * OrderDeleted constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted $resource
     * @param \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted $resource,
        \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted\Collection $resourceCollection,
        array $data = []
    ){
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    public function getId(){
        return $this->getData(self::ID);
    }
    /**
     * @inheritdoc
     */
    public function setId($id){
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getCategoryId(){
        return $this->getData(self::CATEGORY_ID);
    }
    /**
     * @inheritdoc
     */
    public function setCategoryId($categoryId){
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    /**
     * @inheritdoc
     */
    public function getRootCategoryId(){
        return $this->getData(self::ROOT_CATEGORY_ID);
    }
    /**
     * @inheritdoc
     */
    public function setRootCategoryId($rootCategoryId){
        return $this->setData(self::ROOT_CATEGORY_ID, $rootCategoryId);
    }
    /**
     * @inheritdoc
     */
    public function getDeletedAt(){
        return $this->getData(self::DELETED_AT);
    }
    /**
     * @inheritdoc
     */
    public function setDeletedAt($deletedAt){
        return $this->setData(self::DELETED_AT, $deletedAt);
    }
}
