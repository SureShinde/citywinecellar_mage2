<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Pos;


/**
 * Class Pos
 * @package Magestore\Webpos\Model\Pos
 */
class EnterPos extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Pos\EnterPosInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\ResourceModel\Pos\Pos');
    }
    /**
     *  Get Pos Id
     * @return string|null
     */
    public function getPosId()
    {
        return $this->getData(self::POS_ID);
    }

    /**
     * Set Pos Id
     *
     * @param string $posId
     * @return $this
     */
    public function setPosId($posId)
    {
        return $this->setData(self::POS_ID, $posId);
    }

    /**
     *  location_id
     * @return int|null
     */
    public function getLocationId()
    {
        return $this->getData(self::LOCATION_ID);
    }

    /**
     * Set Location Id
     *
     * @param int $locationId
     * @return $this
     */
    public function setLocationId($locationId)
    {
        return $this->setData(self::LOCATION_ID, $locationId);
    }
}