<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Export\StockValue;

/**
 * Class \Magestore\ReportSuccess\Model\Export\StockValue\ConvertToCsv
 */
class ConvertToCsv extends \Magestore\ReportSuccess\Model\Export\AbstractConvertToCsv
{
    /**
     *
     */
    protected $addCategoryInCollection = true;

    /**
     * @var string
     */
    protected $reportTitle = 'STOCK VALUE REPORT';

    /**
     * @var array
     */
    protected $totalData = [
        'qty_on_hand' => 0,
        'stock_value' => 0,
        'potential_revenue' => 0,
        'potential_profit' => 0
    ];

    /**
     * @var array
     */
    protected $priceTotal = [
        'stock_value',
        'mac',
        'price',
        'potential_revenue',
        'potential_profit'
    ];

    /**
     * @var array
     */
    protected $needExplodeItem = ['warehouse', 'supplier'];

    /**
     * @var array
     */
    protected $decimalField = ['qty_on_hand'];

    /**
     * Abstract function
     *
     * Initialize options
     */
    public function setOptions()
    {
        $options = $this->metadataProvider->getOptions();
        $options['supplier'] = $this->supplier->toOptionListArray();
        $options['category_ids'] = $this->getCategoryArray();
        $options['warehouse'] = $this->warehouse->toOptionListArray();
        $filters = $this->_request->getParam('filters');
        if (isset($filters['supplier']) && count($filters['supplier'])
            && isset($options['supplier'][$filters['supplier']])) {
            $options['supplier'] = [$filters['supplier']=>$options['supplier'][$filters['supplier']]];
        }
        $this->options = $options;
    }

    /**
     * Get category array
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoryArray()
    {
        /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
        $collection = $this->categoryCollectionFactory->create()->addAttributeToSelect(['name']);
        $filters = $this->_request->getParam('filters');
        if (isset($filters['category_ids']) && count($filters['category_ids'])) {
            $collection->addFieldToFilter('entity_id', ['in'=>$filters['category_ids']]);
        }
        $data = [];
        foreach ($collection as $_item) {
            $data[$_item->getId()] = $_item->getName();
        }
        return $data;
    }
}
