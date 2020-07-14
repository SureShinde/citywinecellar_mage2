<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Model;

use Magestore\OrderSuccess\Api\Data\OrderInterface;

/**
 * Class Sales
 * @package Magestore\OrderSuccess\Model
 */
class Order extends \Magento\Sales\Model\Order
            implements \Magestore\OrderSuccess\Api\Data\OrderInterface 
{
    /**
     * get batch id
     *
     * @param
     * @return int
     */
    public function getBatchId()
    {
        return $this->getData(self::BATCH_ID);
    }

    /**
     * set batch id
     *
     * @param int $id
     * @return OrderInterface
     */
    public function setBatchId($id)
    {
        return $this->setData(self::BATCH_ID, $id);
    }

    /**
     * get tag color
     *
     * @param
     * @return string
     */
    public function getTagColor()
    {
        return $this->getData(self::TAG_COLOR);
    }

    /**
     * set tag color
     *
     * @param string $code
     * @return OrderInterface
     */
    public function setTagColor($tag)
    {
        return $this->setData(self::TAG_COLOR, $tag);
    }

    /**
     * get is verified
     *
     * @param
     * @return int
     */
    public function getIsVerified()
    {
        return $this->getData(self::IS_VERIFIED);
    }

    /**
     * set is verified
     *
     * @param int $id
     * @return OrderInterface
     */
    public function setIsVerified($isVerified)
    {
        return $this->setData(self::IS_VERIFIED, $isVerified);
    }
}
