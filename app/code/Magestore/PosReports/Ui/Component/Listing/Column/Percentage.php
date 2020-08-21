<?php

/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Percentage
 *
 * Used to create Percentage
 */
class Percentage extends Column
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
                if (isset($item[$this->getData('name')])) {
                    $item[$this->getData('name')] = sprintf(
                        '%s%%',
                        number_format($item[$this->getData('name')], 2)
                    );
                }
            }
        }

        return $dataSource;
    }
}
