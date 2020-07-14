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
class CurrencyRepository implements \Magestore\PosSampleData\Api\CurrencyRepositoryInterface
{
    protected $currencyModel;

    /**
     * CurrencyRepository constructor.
     * @param \Magento\Directory\Model\Currency $currencyModel
     */
    public function __construct(
        \Magento\Directory\Model\Currency $currencyModel
    ){
        $this->currencyModel = $currencyModel;
    }

    /**
     * @inheritdoc
     */
    public function changeRate(\Magestore\PosSampleData\Api\Data\CurrencyRateInterface $currencyRate){
        $data[$currencyRate->getCurrencyCode()][$currencyRate->getCurrencyTo()] = $currencyRate->getRate();
        $this->currencyModel->saveRates($data);

        return true;
    }
}