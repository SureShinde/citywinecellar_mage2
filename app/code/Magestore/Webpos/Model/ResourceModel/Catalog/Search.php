<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Catalog;

class Search extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('webpos_search_product', 'entity_id');
    }
    
    /**
     * Set store ID for resource model
     * 
     * @param int $storeId
     */
    public function setStoreId($storeId)
    {
        $this->_init(sprintf('webpos_search_product_%s', $storeId), 'entity_id');
    }
}
