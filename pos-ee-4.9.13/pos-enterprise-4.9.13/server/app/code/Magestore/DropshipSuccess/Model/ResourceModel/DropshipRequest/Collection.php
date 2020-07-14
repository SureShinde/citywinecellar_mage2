<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest;

use Magento\Framework\DB\Select;

/**
 * Class DropshipRequest
 * @package Magestore\SupplierSuccess\Model\ResourceModel\Supplier
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'dropship_request_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\DropshipSuccess\Model\DropshipRequest', 'Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest');
    }

    /**
     * @param $supplierId
     * @return $this
     */
    public function getDropshipRequestBySupplierId($supplierId)
    {
        $this->addFieldToFilter('supplier_id', $supplierId)
            ->setOrder(\Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface::CREATED_AT , 'DESC');
        $this->getSelect()
            ->joinLeft(
                ['order' => $this->getTable('sales_order_grid')],
                'main_table.order_id = order.entity_id',
                []
            )->columns(
                [
                    'shipping_name' => 'order.shipping_name',
                    'shipping_email' => 'order.customer_email',
                ]
        );
        return $this;
    }
}