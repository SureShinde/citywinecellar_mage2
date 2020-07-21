<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class PointsColumn extends Column
{

    const FIELD_NAME = 'amrewardpoints';

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = $item[self::FIELD_NAME] ? $item[self::FIELD_NAME] : 0;
            }
        }

        return $dataSource;
    }
}
