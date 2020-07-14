<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Model;

use Magestore\OrderSuccess\Api\Data\BatchInterface;

/**
 * Class Batch
 * @package Magestore\OrderSuccess\Model
 */
class Batch extends \Magento\Framework\Model\AbstractModel
            implements \Magestore\OrderSuccess\Api\Data\BatchInterface
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->_init('Magestore\OrderSuccess\Model\ResourceModel\Batch');
    }

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
     * @return BatchInterface
     */
    public function setBatchId($id)
    {
        return $this->setData(self::BATCH_ID, $id);
    }

    /**
     * get batcg code
     *
     * @param
     * @return string
     */
    public function getCode()
    {
        return self::PREFIX . $this->getData(self::BATCH_ID);
    }

    /**
     * set code
     *
     * @param string $code
     * @return BatchInterface
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * get user id
     *
     * @param
     * @return int
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * set user id
     *
     * @param int $id
     * @return BatchInterface
     */
    public function setUserId($id)
    {
        return $this->setData(self::USER_ID, $id);
    }
}
