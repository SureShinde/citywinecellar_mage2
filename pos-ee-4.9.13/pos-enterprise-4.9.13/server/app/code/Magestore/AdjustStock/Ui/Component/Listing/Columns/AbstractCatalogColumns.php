<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Ui\Component\Listing\Columns;

/**
 * Class Actions.
 *
 * @category Magestore
 * @package  Magestore_AdjustStock
 * @module   Inventorysuccess
 * @author   Magestore Developer
 */
class AbstractCatalogColumns extends \Magento\Catalog\Ui\Component\Listing\Columns
{
    protected $columnsThumbnail = 'image';

    public function prepare()
    {
        $ret = parent::prepare();

        $this->_prepareColumns();
        return $ret;
    }

    public function _prepareColumns()
    {
        foreach ($this->components as $id => $column) {
            if ($column instanceof \Magento\Ui\Component\Listing\Columns\Column) {
                if(!$this->checkProductSource() && ($id == $this->columnsThumbnail)) {
                    unset($this->components[$id]);
                }
            }
        }
    }

    public function checkProductSource() {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $om->get('Magestore\AdjustStock\Api\AdjustStock\AdjustStockManagementInterface');
        return $helper->isShowThumbnail();
    }
}
