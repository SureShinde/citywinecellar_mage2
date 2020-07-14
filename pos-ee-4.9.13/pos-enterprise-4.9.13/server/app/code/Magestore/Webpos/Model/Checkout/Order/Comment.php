<?php

namespace Magestore\Webpos\Model\Checkout\Order;

use Magestore\Webpos\Api\Data\Checkout\Order\CommentInterface;

class Comment extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Checkout\Order\CommentInterface
{
    /**
     * @inheritdoc
     */
    public function getCreatedAt() {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt) {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId() {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId) {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getIsVisibleOnFront() {
        return $this->getData(self::IS_VISIBLE_ON_FRONT);
    }

    /**
     * @inheritdoc
     */
    public function setIsVisibleOnFront($isVisibleOnFront) {
        return $this->setData(self::IS_VISIBLE_ON_FRONT, $isVisibleOnFront);
    }

    /**
     * @inheritdoc
     */
    public function getComment() {
        return $this->getData(self::COMMENT);
    }

    /**
     * @inheritdoc
     */
    public function setComment($comment) {
        return $this->setData(self::COMMENT, $comment);
    }
    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\Order\CommentExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}