<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\FulfilSuccess\Model\PackRequest\PackRequestItem', 'Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem');
    }
    
    /**
     * 
     * @param array $fields
     * @return \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem\Collection
     */
    public function joinPackRequest($fields=[])
    {
        $fields = count($fields) ? $fields : '*';
        $this->getSelect()->join(
                ['packRequest' => $this->getTable('os_fulfilsuccess_packrequest')],
                ' main_table.pack_request_id = packRequest.pack_request_id',
                $fields
        );
        return $this;        
    }    

}
