<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Api\Data;

/**
 * Interface OrderInterface
 * @package Magestore\OrderSuccess\Api\Data
 */
interface OrderInterface extends \Magento\Sales\Api\Data\OrderInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const POSITION = 'position';
    const TAG_COLOR = 'tag_color';
    const IS_VERIFIED = 'is_verified';
    const BATCH_ID = 'batch_id';

    /**
     * get tag color
     *
     * @return string
     */
    public function getTagColor();

    /**
     * set tag color
     *
     * @param string $tag
     * @return OrderInterface
     */
    public function setTagColor($tag);

    /**
     * get is verified
     *
     * @return int
     */
    public function getIsVerified();

    /**
     * set is verified
     *
     * @param int $isVerified
     * @return OrderInterface
     */
    public function setIsVerified($isVerified);

    /**
     * get batch id
     *
     * @return int
     */
    public function getBatchId();

    /**
     * set batch id
     *
     * @param int $batchId
     * @return OrderInterface
     */
    public function setBatchId($batchId);


}