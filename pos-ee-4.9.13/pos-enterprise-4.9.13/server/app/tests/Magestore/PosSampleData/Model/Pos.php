<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Model;
/**
 * Class Currency
 * @package Magestore\Webpos\Model\Config\Data
 */
class Pos extends \Magento\Framework\DataObject implements \Magestore\PosSampleData\Api\Data\PosInterface
{
    /**
     * Get pos name
     *
     * @api
     * @return string
     */
    public function getPosName(){
        return $this->getData(self::POS_NAME);
    }

    /**
     * Set pos name
     *
     * @api
     * @param string $posName
     * @return \Magestore\PosSampleData\Api\Data\PosInterface
     */
    public function setPosName($posName){
        return $this->setData(self::POS_NAME, $posName);
    }

    /**
     * Get username
     *
     * @api
     * @return string
     */
    public function getUsername(){
        return $this->getData(self::USERNAME);
    }

    /**
     * Set username
     *
     * @api
     * @param string $username
     * @return \Magestore\PosSampleData\Api\Data\PosInterface
     */
    public function setUsername($username){
        return $this->setData(self::USERNAME, $username);
    }
}