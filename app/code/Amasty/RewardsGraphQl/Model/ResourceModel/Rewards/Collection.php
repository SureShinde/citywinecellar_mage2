<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RewardsGraphQl
 */


namespace Amasty\RewardsGraphQl\Model\ResourceModel\Rewards;

use Amasty\Rewards\Model\ResourceModel\Expiration;
use Amasty\Rewards\Model\ResourceModel\Rewards\Collection as RewardsCollection;

class Collection extends RewardsCollection
{
    /**
     * @return RewardsCollection
     */
    public function addExpirationDate()
    {
        $this->getSelect()->joinLeft(
            ['expiration' => $this->getTable(Expiration::TABLE_NAME)],
            'main_table.expiration_id = expiration.entity_id',
            ['expiration_date' => 'expiration.date']
        )->order('main_table.id DESC');

        return $this;
    }
}
