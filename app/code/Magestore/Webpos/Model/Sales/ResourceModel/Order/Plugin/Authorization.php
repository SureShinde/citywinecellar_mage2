<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Sales\ResourceModel\Order\Plugin;

use Magento\Sales\Model\Order;

/**
 * Class OrderRepository
 * @package Magestore\Webpos\Model\Sales
 */
class Authorization extends \Magento\Sales\Model\ResourceModel\Order\Plugin\Authorization
{
    /**
     * Checks if order is allowed
     *
     * @param \Magento\Sales\Model\ResourceModel\Order $subject
     * @param callable $proceed
     * @param \Magento\Framework\Model\AbstractModel $order
     * @param mixed $value
     * @param null|string $field
     * @return \Magento\Sales\Model\Order
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundLoad(
        \Magento\Sales\Model\ResourceModel\Order $subject,
        \Closure $proceed,
        \Magento\Framework\Model\AbstractModel $order,
        $value,
        $field = null
    )
    {
        if ($order instanceof \Magento\Sales\Model\Order) {
            $function = 'aroundLoad';
            foreach (class_parents($this) as $parent) {
                if (method_exists($parent, 'aroundLoad')) {
                    //do something, for instance:
                    return $parent::$function($subject, $proceed, $order, $value, $field);
                    break;
                }
            }
        }
        return $proceed($order, $value, $field);
    }
}
