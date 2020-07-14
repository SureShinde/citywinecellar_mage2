<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Api;

interface CurrencyRepositoryInterface
{
    /**
     * Save Role.
     *
     * @param \Magestore\PosSampleData\Api\Data\CurrencyRateInterface $currencyRate
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function changeRate(\Magestore\PosSampleData\Api\Data\CurrencyRateInterface $currencyRate);

}
