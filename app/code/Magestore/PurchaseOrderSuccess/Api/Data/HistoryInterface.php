<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface HistoryInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    
    const PURCHASE_ORDER_HISTORY_ID = 'purchase_order_history_id';
    
    const PURCHASE_ORDER_ID = 'purchase_order_id';
    
    const USER_NAME = 'user_name';
    
    const USER_ID = 'user_id';

    const CONTENT = 'content';

    const OLD_VALUE = 'old_value';

    const NEW_VALUE = 'new_value';
    
    const CREATED_AT = 'created_at';
    
    /**#@-*/

    /**
     * Get purchase order history id
     *
     * @return int
     */
    public function getPurchaseOrderHistoryId();

    /**
     * Set purchase order history id
     *
     * @param int $purchaseOrderHistoryId
     * @return $this
     */
    public function setPurchaseOrderHistoryId($purchaseOrderHistoryId);

    /**
     * Get purchase order id
     *
     * @return int
     */
    public function getPurchaseOrderId();

    /**
     * Set purchase order id
     *
     * @param int $purchaseOrderId
     * @return $this
     */
    public function setPurchaseOrderId($purchaseOrderId);

    /**
     * Get user name
     *
     * @return string
     */
    public function getUserName();

    /**
     * Set user name
     *
     * @param string $userName
     * @return $this
     */
    public function setUserName($userName);

    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId();

    /**
     * Set user id
     *
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId);

    /**
     * Get content
     *
     * @return string
     */
    public function getContent();

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * Get old value
     *
     * @return string
     */
    public function getOldValue();

    /**
     * Set old value
     *
     * @param string $oldValue
     * @return $this
     */
    public function setOldValue($oldValue);

    /**
     * Get user name
     *
     * @return string
     */
    public function getNewValue();

    /**
     * Set user name
     *
     * @param string $userName
     * @return $this
     */
    public function setNewValue($newValue);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);
}