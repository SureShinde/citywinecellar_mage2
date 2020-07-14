<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Export;
use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Class MetadataProvider
 * @package Magestore\ReportSuccess\Model\Export
 */
class MetadataProvider extends \Magento\Ui\Model\Export\MetadataProvider
{
    /**
     * Retrieve Headers row array for Export
     *
     * @param UiComponentInterface $component
     * @return string[]
     */
    public function getColumnsData(UiComponentInterface $component)
    {
        $row = [];
        foreach ($this->getColumns($component) as $column) {
            $row[$column->getData('name')] = $column->getData('config/label');
        }
        return $row;
    }
}