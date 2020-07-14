<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Log;

class DataLogResults extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Log\DataLogResultsInterface
{
    /**
     * Get ids
     *
     * @return array
     */
    public function getIds(){
        return $this->getData(self::IDS);
    }
    /**
     * Set ids
     *
     * @param array $ids
     * @return $this
     */
    public function setIds($ids){
        return $this->setData(self::IDS, $ids);
    }
}