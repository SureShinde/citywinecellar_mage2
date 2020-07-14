<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Model;

use Magestore\BarcodeSuccess\Api\Data\HistoryInterface;

class History  extends \Magento\Framework\Model\AbstractModel implements HistoryInterface
{
    /**
     * generate barcode
     */
    const GENERATED = 1;

    /**
     * import barcode
     */
    const IMPORTED = 2;
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\BarcodeSuccess\Model\ResourceModel\History');
    }

    /**
     * History id
     *
     * @return int|null
     */
    public function getId(){
        return $this->_getData(self::ID);
    }

    /**
     * Set history id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id){
        return $this->setData(self::ID, $id);
    }

    /**
     * get created at
     *
     * @return string|null
     */
    public function getCreatedAt(){
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * get created by
     *
     * @return string|null
     */
    public function getCreatedBy(){
        return $this->_getData(self::CREATED_BY);
    }

    /**
     * Set created by
     *
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy){
        return $this->setData(self::CREATED_BY, $createdBy);
    }

    /**
     * get reason
     *
     * @return string|null
     */
    public function getReason(){
        return $this->_getData(self::REASON);
    }

    /**
     * Set reason
     *
     * @param string $reason
     * @return $this
     */
    public function setReason($reason){
        return $this->setData(self::REASON, $reason);
    }

    /**
     * get total qty
     *
     * @return string|null
     */
    public function getTotalQty(){
        return $this->_getData(self::TOTAL_QTY);
    }

    /**
     * Set total qty
     *
     * @param string $totalQty
     * @return $this
     */
    public function setTotalQty($totalQty){
        return $this->setData(self::TOTAL_QTY, $totalQty);
    }

    /**
     * get type
     *
     * @return string|null
     */
    public function getType(){
        return $this->_getData(self::TYPE);
    }

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type){
        return $this->setData(self::TYPE, $type);
    }
}