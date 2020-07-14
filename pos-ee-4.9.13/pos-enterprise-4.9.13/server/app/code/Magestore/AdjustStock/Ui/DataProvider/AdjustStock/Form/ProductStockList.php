<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Ui\DataProvider\AdjustStock\Form;

/**
 * Class ProductStockList
 * @package Magestore\AdjustStock\Ui\DataProvider\AdjustStock\Form
 */
class ProductStockList extends \Magestore\AdjustStock\Ui\DataProvider\Form\Modifier\ProductStockList
{
    /**
     * @var array
     */
    protected $addedField;

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->addedField[] = $filter->getField();
        if ($filter->getField() == "source_code" && $filter->getValue()) {
            $this->getCollection()->addSourceCodeToFilter($filter->getValue());
        } else if ($filter->getField() == "barcode" && $filter->getValue()) {
            $this->getCollection()->addBarcodeToFilter($filter->getValue());
        } else {
            parent::addFilter($filter);
        }
    }
}
