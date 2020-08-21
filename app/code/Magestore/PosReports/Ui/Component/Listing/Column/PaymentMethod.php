<?php

/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class PaymentMethod
 *
 * Used to create Payment Method
 */
class PaymentMethod extends Column
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
                $methodTitle = isset($item['method_title']) ? $item['method_title'] : "";
                if (!empty($methodTitle)) {
                    $item[$this->getData('name')] = $methodTitle;
                }
            }
        }

        return $dataSource;
    }
}
