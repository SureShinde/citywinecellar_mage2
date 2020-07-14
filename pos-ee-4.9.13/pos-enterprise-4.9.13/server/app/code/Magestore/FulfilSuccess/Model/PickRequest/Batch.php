<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\PickRequest;

use Magestore\FulfilSuccess\Api\Data\BatchInterface;

class Batch extends \Magento\Framework\Model\AbstractModel implements BatchInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\Batch');
    }
    
   /**
     * get Batch id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_getData(self::BATCH_ID);
    }
    
    /**
     * get Batch id
     *
     * @return int|null
     */
    public function getBatchId()
    {
        return $this->_getData(self::BATCH_ID);
    }
    
    /**
     * get Code of Batch
     *
     * @return string
     */
    public function getCode()
    {
        return self::PREFIX . $this->_getData(self::BATCH_ID);
    }

    /**
     * get User id
     *
     * @return int|null
     */
    public function getUserId()
    {
        return $this->_getData(self::USER_ID);
    }
    
    /**
     * set Batch ID
     * 
     * @param int $id
     */
    public function setId($id)
    {
        return $this->setData(self::BATCH_ID, $id);
    }
    
    /**
     * set Batch ID
     * 
     * @param int $batchId
     */
    public function setBatchId($batchId)
    {
        return $this->setData(self::BATCH_ID, $batchId);
    }

    /**
     * set Code
     * 
     * @param string $code
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * set User ID
     * 
     * @param int $userId
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }
}