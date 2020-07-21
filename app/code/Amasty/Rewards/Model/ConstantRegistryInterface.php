<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model;

interface ConstantRegistryInterface
{
    /**#@+
     * Customer Rewards Notify Attrs
     */
    const NOTIFICATION_EARNING = 'amrewards_earning_notification';

    const NOTIFICATION_EXPIRE = 'amrewards_expire_notification';

    const CURRENT_REWARD = 'current_amasty_rewards_rule';

    const FORM_NAMESPACE = 'amasty_rewards_rule_form';
    /**#@-*/

    /**
     * Key for Registry
     */
    const CUSTOMER_STATISTICS = 'current_amasty_rewards_statistic';

    /**
     * Store ID key
     */
    const STORE_ID = 'store_id';
}
