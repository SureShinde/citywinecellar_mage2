<?php
/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PosReports\Ui\DataProvider\Reports;

/**
 * Class AbstractDataProvider
 *
 * Used to create Abstract Data Provider
 */
class AbstractDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return [
            "" => []
        ];
    }
}
