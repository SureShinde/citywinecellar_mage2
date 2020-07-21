<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\ResourceModel;

use Magento\Customer\Model\ResourceModel\Visitor;

class CustomerVisitor
{
    /**
     * @var Visitor
     */
    private $visitor;

    public function __construct(
        Visitor $visitor
    ) {
        $this->visitor = $visitor;
    }

    /**
     * @param string $toDate
     *
     * @return array
     */
    public function getInactiveCustomers($toDate)
    {
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
        $connection = $this->visitor->getConnection();
        $ignore = $connection->select()->from(
            ['visitor_table' => $this->visitor->getTable('customer_visitor')],
            ['visitor_table.customer_id']
        )->where(
            'visitor_table.last_visit_at > ?',
            $toDate
        )->where(
            'visitor_table.customer_id IS NOT NULL'
        )->group(
            'visitor_table.customer_id'
        );

        $select = $connection->select()->from(
            ['visitor_table' => $this->visitor->getTable('customer_visitor')],
            ['visitor_table.customer_id']
        )->where(
            'visitor_table.customer_id NOT IN(?)',
            $ignore
        )->where(
            'visitor_table.customer_id IS NOT NULL'
        )->group(
            'visitor_table.customer_id'
        );

        return $connection->fetchAll($select, []);
    }
}
