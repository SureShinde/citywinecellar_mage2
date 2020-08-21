<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel\History;

/**
 * Giftvoucher history resource collection
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Magestore\Giftvoucher\Model\History::class,
            \Magestore\Giftvoucher\Model\ResourceModel\History::class
        );
    }

    /**
     * Join Giftcode for Grid of Customer
     *
     * @return \Magestore\Giftvoucher\Model\ResourceModel\History\Collection
     */
    public function joinGiftcodeForGrid()
    {
        if ($this->hasFlag('join_giftcode') && $this->getFlag('join_giftcode')) {
            return $this;
        }
        $this->setFlag('join_giftcode', true);
        $this->getSelect()->joinLeft(
            ['giftvoucher' => $this->getTable('giftvoucher')],
            'main_table.giftvoucher_id = giftvoucher.giftvoucher_id',
            [
                'gift_code'
            ]
        );
        return $this;
    }

    /**
     * Join Gift Voucher
     *
     * @return $this
     */
    public function joinGiftVoucher()
    {
        if ($this->hasFlag('join_giftvoucher') && $this->getFlag('join_giftvoucher')) {
            return $this;
        }
        $this->setFlag('join_giftvoucher', true);
        $this->getSelect()->joinLeft(
            ['giftvoucher' => $this->getTable('giftvoucher')],
            'main_table.giftvoucher_id = giftvoucher.giftvoucher_id',
            [
                'gift_code'
            ]
        )->where('main_table.action = ?', \Magestore\Giftvoucher\Model\Actions::ACTIONS_SPEND_ORDER);
        return $this;
    }

    /**
     * Join Sales Order
     *
     * @return $this
     */
    public function joinSalesOrder()
    {
        $this->getSelect()->joinLeft(
            ['o' => $this->getTable('sales_order')],
            'main_table.order_increment_id = o.increment_id',
            ['order_customer_id' => 'customer_id']
        )->group('o.customer_id');

        return $this;
    }

    /**
     * Get History
     *
     * @return $this
     */
    public function getHistory()
    {
        $this->getSelect()->order('main_table.created_at DESC');
        $this->getSelect()
            ->joinLeft(
                ['o' => $this->getTable('sales_order')],
                'main_table.order_increment_id = o.increment_id',
                ['order_id' => 'entity_id']
            );

        return $this;
    }
}
