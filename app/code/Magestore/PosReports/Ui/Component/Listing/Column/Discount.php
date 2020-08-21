<?php

/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Ui\Component\Listing\Column;

/**
 * Class Discount
 *
 * Used to create Discount
 */
class Discount extends Price
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items']) && !empty($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $currencyCode = isset($item['base_currency_code']) ? $item['base_currency_code'] : null;
                if (isset($item[$this->getData('name')])) {
                    $amount = $item[$this->getData('name')];
                    $item[$this->getData('name')] = $this->priceFormatter->format(
                        ($amount > 0) ? -$amount : 0,
                        false,
                        null,
                        null,
                        $currencyCode
                    );
                }
            }
        }

        return $dataSource;
    }
}
