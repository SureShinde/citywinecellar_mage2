<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Api\Data;

/**
 * Interface BatchInterface
 * @package Magestore\OrderSuccess\Api\Data
 */
interface BatchInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const BATCH_ID = 'batch_id';
    const CODE = 'code';
    const USER_ID = 'user_id';

    /**
     * orefix code
     */
    const PREFIX = 'BATCH';

    /**
     * get batch Id
     *
     * @return int
     */
    public function getBatchId();

    /**
     * set batch Id
     *
     * @param int $id
     * @return BatchInterface
     */
    public function setBatchId($id);

    /**
     * Get label
     *
     * @return string
     */
    public function getCode();

    /**
     * set code
     *
     * @param string $code
     * @return BatchInterface
     */
    public function setCode($code);

    /**
     * get user Id
     *
     * @return int
     */
    public function getUserId();

    /**
     * set user Id
     *
     * @param int $id
     * @return BatchInterface
     */
    public function setUserId($id);
}