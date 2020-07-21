<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

namespace Amasty\Rewards\Model;

use Magento\Framework\Model\AbstractModel;
use Amasty\Rewards\Api\Data\QuoteInterface;

class Quote extends AbstractModel implements QuoteInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Rewards\Model\ResourceModel\Quote');
    }

    /**
     * @param $quoteId
     * @param $amount
     * @deprecated since 1.12.0
     */
    public function addReward($quoteId, $amount)
    {
        $quote =  $this->getResource()->loadByQuoteId($quoteId);
        if (!$quote) {
            $this->addData([
                'quote_id'      => $quoteId,
                'reward_points' => $amount

            ]);
            $this->save();
        } else {
            $this->addData([
                'id'            => $quote['id'],
                'quote_id'      => $quoteId,
                'reward_points' => $amount

            ]);
            $this->save();
        }
    }

    /**
     * @inheritdoc
     */
    public function getQuoteId()
    {
        return $this->_getData(QuoteInterface::QUOTE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setQuoteId($quoteId)
    {
        $this->setData(QuoteInterface::QUOTE_ID, $quoteId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRewardPoints()
    {
        return $this->_getData(QuoteInterface::REWARD_POINTS);
    }

    /**
     * @inheritdoc
     */
    public function setRewardPoints($rewardPoints)
    {
        $this->setData(QuoteInterface::REWARD_POINTS, $rewardPoints);

        return $this;
    }
}
