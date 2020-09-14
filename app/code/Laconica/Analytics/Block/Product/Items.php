<?php

namespace Laconica\Analytics\Block\Product;

use Magento\Framework\Exception\NoSuchEntityException;

class Items extends \Laconica\Analytics\Block\Html\Gtm
{
    /**
     * @return bool|false|string
     */
    public function getPushJson()
    {
        $items = $this->getData('products');
        if (!$items || !is_array($items)) {
            return false;
        }
        $impressions = [];
        $counter = 0;
        foreach ($items as $item) {
            if ($counter >= $this->configHelper->getWidgetExpressionsLimit()) {
                break;
            }
            if (!$item || !$item->getId()) {
                continue;
            }
            array_push($impressions, [
                'name' => $item->getName(),
                'price' => $this->configHelper->formatPrice($item->getFinalPrice())
            ]);
            $counter++;
        }
        try {
            $data = [
                'event' => 'productList',
                'ecommerce' => [
                    'currencyCode' => $this->_storeManager->getStore()->getCurrentCurrency()->getCode(),
                    'impressions' => $impressions
                ]
            ];
        } catch (NoSuchEntityException $e) {
            return false;
        }
        return json_encode($data);
    }
}