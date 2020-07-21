<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Api\Data;

interface ExpirationArgumentsInterface
{
    const IS_EXPIRE = 'is_expire';
    const DAYS = 'days';

    /**
     * @param bool $expire
     *
     * @return ExpirationArgumentsInterface
     */
    public function setIsExpire($expire);

    /**
     * @return bool
     */
    public function isExpire();

    /**
     * @param int $days
     *
     * @return ExpirationArgumentsInterface
     */
    public function setDays($days);

    /**
     * @return int
     */
    public function getDays();
}
