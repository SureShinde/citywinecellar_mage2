<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\ResourceModel;

class Quote extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize connection and define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_rewards_quote', 'id');
    }

    public function loadByQuoteId($quoteId)
    {
        $select = $this->getConnection()->select()->from($this->getMainTable())->where('quote_id=:quote_id');

        $result = $this->getConnection()->fetchRow(
            $select,
            [
                'quote_id' => $quoteId
            ]
        );

        if (!$result) {
            return [];
        }

        return $result;
    }

    /**
     * @param int $quoteId
     *
     * @return float
     */
    public function getUsedRewards($quoteId)
    {
        $quote = $this->loadByQuoteId($quoteId);

        return empty($quote['reward_points']) ? 0 : floatval($quote['reward_points']);
    }
}
