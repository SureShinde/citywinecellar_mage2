<?php

namespace Laconica\ShippingTableRates\Plugin;

use Magento\Quote\Model\Quote\Address\Rate as PluginRate;

class Rate
{
    /**
     * @param PluginRate $rate
     * @param $proceed
     * @param $rates
     * @return mixed
     */
    public function aroundImportShippingRate(PluginRate $rate, $proceed, $rates)
    {
        $isTips = $rates->getIsTips();
        $returnValue = $proceed($rates);

        if (!is_null($isTips)) {
            $returnValue->setIsTips($isTips);
        }

        return $returnValue;
    }
}
