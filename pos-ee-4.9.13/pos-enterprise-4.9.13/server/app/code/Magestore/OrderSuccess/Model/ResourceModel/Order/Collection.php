<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Model\ResourceModel\Order;

/**
 * Class \Magestore\OrderSuccess\Model\ResourceModel\Sales\Collection
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
{
    /**
     * @var \Magestore\OrderSuccess\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Construct declare injection
     *
     * @return void
     */
    public function _construct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->helper = $objectManager->get(\Magestore\OrderSuccess\Helper\Data::class);
        $this->dateTime = $objectManager->get(\Magento\Framework\Stdlib\DateTime\Datetime::class);
        return parent::_construct();
    }

    /**
     * Init select for sql
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $offset = $this->dateTime->getGmtOffset('seconds');
        if ($offset > 0) {
            $convertDate = 'DATE_ADD(order_history.created_at, INTERVAL ' . $offset . ' SECOND)';
        } elseif ($offset < 0) {
            $convertDate = 'DATE_SUB(order_history.created_at, INTERVAL ' . abs($offset) . ' SECOND)';
        } else {
            $convertDate = 'order_history.created_at';
        }
        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->columns(
                [
                    'age' => new \Zend_Db_Expr('TIMESTAMPDIFF(SECOND, main_table.created_at, NOW())')
                ]
            );
        $this->addFilterToMap(
            'entity_id',
            'main_table.entity_id'
        )->addFilterToMap(
            'customer_id',
            'main_table.customer_id'
        )->addFilterToMap(
            'quote_address_id',
            'main_table.quote_address_id'
        );

        $commentSelect = clone $this->getSelect();
        $commentSelect->reset();
        $commentSelect->from(['order_history' => $this->getTable('sales_order_status_history')]);
        $commentSelect->columns(
            [
                'note' => new \Zend_Db_Expr(
                    'GROUP_CONCAT(
                        CONCAT(
                            "\n", 
                            DATE_FORMAT(' . $convertDate . ',\'%b %d %Y %h:%i %p\'),
                            ": ", 
                            order_history.comment
                        ) SEPARATOR "\n"
                    )'
                )
            ]
        );
        $commentSelect->group('order_history.parent_id');

        $this->getSelect()->joinLeft(
            ['order_history' => $commentSelect],
            'main_table.entity_id = order_history.parent_id',
            [
                'order_history_id' => 'order_history.entity_id',
                'note' => 'order_history.note',
            ]
        );

        $this->getSelect()->joinLeft(
            ['sales_order' => $this->getTable('sales_order')],
            'main_table.entity_id = sales_order.entity_id',
            [
                'tag_color' => 'sales_order.tag_color',
                'is_verified' => 'sales_order.is_verified',
                'total_due' => 'sales_order.total_due',
                'batch_id' => 'sales_order.batch_id'
            ]
        );
        $this->getSelect()->group('main_table.entity_id');
        $this->addCondition();

        return $this;
    }

    /**
     * Rewrite add field to filters from collection
     *
     * @param array|string $field
     * @param null|array $condition
     * @return \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'increment_id') {
            $field = 'main_table.increment_id';
        }
        if ($field == 'status') {
            $field = 'main_table.status';
        }
        if ($field == 'created_at') {
            $field = 'main_table.created_at';
        }
        if ($field == 'grand_total') {
            $field = 'main_table.grand_total';
        }
        if ($field == 'age') {
            $field = new \Zend_Db_Expr('TIMESTAMPDIFF(SECOND, main_table.created_at, NOW())');
        }
        if ($field == 'tag_color') {
            $field = 'sales_order.tag_color';
            $condition = ['like' => '%' . $condition['eq'] . '%'];
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Get current page.
     *
     * @return int|null
     */
    public function getCurrentPage()
    {
        return $this->getCurPage();
    }

    /**
     * Set current page.
     *
     * @param int $currentPage
     * @return $this
     */
    public function setCurrentPage($currentPage)
    {
        return $this->setCurPage($currentPage);
    }

    /**
     * Add condition.
     *
     * @param
     * @return $this
     */
    public function addCondition()
    {
        return $this;
    }

    /**
     * Override getData
     *
     * @return $data
     */
    public function getData()
    {
        $data = parent::getData();
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $timeZone = $om->get(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::class);
        $requestInterface = $om->get(\Magento\Framework\App\RequestInterface::class);
        if ($requestInterface->getActionName() == 'gridToCsv' || $requestInterface->getActionName() == 'gridToXml') {
            foreach ($data as &$item) {
                $item['created_at'] = $timeZone->date($item['created_at'])->format('Y-m-d H:i:s');
            }
        }
        return $data;
    }
}
