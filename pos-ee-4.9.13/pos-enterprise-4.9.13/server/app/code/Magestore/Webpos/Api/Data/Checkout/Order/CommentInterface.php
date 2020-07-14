<?php

namespace Magestore\Webpos\Api\Data\Checkout\Order;

interface CommentInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ENTITY_ID = 'entity_id';
    const IS_VISIBLE_ON_FRONT = 'is_visible_on_front';
    const COMMENT = 'comment';
    const CREATED_AT = 'created_at';

    /**
     * Gets the created-at timestamp for the order status history.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt();

    /**
     * Sets the created-at timestamp for the order status history.
     *
     * @param string|null $createdAt timestamp
     * @return CommentInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Gets the ID for the order status history.
     *
     * @return int|null Order status history ID.
     */
    public function getEntityId();

    /**
     * Sets entity ID.
     *
     * @param int|null $entityId
     * @return CommentInterface
     */
    public function setEntityId($entityId);


    /**
     * Gets the is-visible-on-storefront flag value for the order status history.
     *
     * @return int|null Is-visible-on-storefront flag value.
     */
    public function getIsVisibleOnFront();

    /**
     * Sets the is-visible-on-storefront flag value for the order status history.
     *
     * @param int|null $isVisibleOnFront
     * @return CommentInterface
     */
    public function setIsVisibleOnFront($isVisibleOnFront);

    /**
     * Gets the comment for the order status history.
     *
     * @return string|null Comment.
     */
    public function getComment();

    /**
     * Sets the comment for the order status history.
     *
     * @param string|null $comment
     * @return CommentInterface
     */
    public function setComment($comment);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\CommentExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\CommentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\Order\CommentExtensionInterface $extensionAttributes
    );
}
