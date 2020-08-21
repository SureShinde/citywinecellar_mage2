<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Export;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Filters;
use Magento\Ui\Component\Filters\Type\Select;

/**
 * Class MetadataProvider
 *
 * Used for metadata provider
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

    /**
     * Returns Filters with options
     *
     * @return array
     */
    public function getOptions(): array
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $metadataProvider = $objectManager->get(\Magento\Ui\Model\Export\MetadataProvider::class);
        if (method_exists($metadataProvider, 'getColumnOptions')) {
            $options = [];
            $component = $this->filter->getComponent();
            $childComponents = $component->getChildComponents();
            $listingTop = $childComponents['listing_top'];
            foreach ($listingTop->getChildComponents() as $child) {
                if ($child instanceof Filters) {
                    foreach ($child->getChildComponents() as $filter) {
                        if ($filter instanceof Select) {
                            $options[$filter->getName()] = $this->getFilterOptions($filter);
                        }
                    }
                }
            }
        } else {
            $options = parent::getOptions();
        }
        return $options;
    }
}
