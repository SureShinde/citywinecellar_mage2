<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Plugin\Sales\Model;

/**
 * Class AfterGetSimpleQtyToShip
 * @package Magestore\OrderSuccess\Plugin\Item
 */
class Order
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    public function __construct
    (
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->request = $request;
    }

    /**
     * Retrieve order shipment availability
     *
     * @param \Magento\Sales\Model\Order $order
     * @param bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function afterCanShip(\Magento\Sales\Model\Order $order, $result)
    {
        if ($result == false) {
            if ($order->canUnhold() || $order->isPaymentReview()) {
                return false;
            }

            if ($order->getIsVirtual() || $order->isCanceled()) {
                return false;
            }

            if ($order->getActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_SHIP) === false) {
                return false;
            }
            foreach ($order->getAllItems() as $item) {
                $qtyToShip = $item->getQtyToShip();
                if ($this->needCalculateQtyPrepareShip()) {
                    $qtyToShip += $item->getQtyPrepareship();
                }
                if ($qtyToShip > 0 && !$item->getIsVirtual() && !$item->getLockedDoShip()) {
                    return true;
                }
            }
            return false;
        }
        return $result;
    }

    /**
     * TODO need to seek another solution
     * @return bool
     */
    public function needCalculateQtyPrepareShip()
    {
        $moduleName = $this->request->getModuleName();
        $controllerName = $this->request->getControllerName();
        $actionName = $this->request->getActionName();
        $params = $this->request->getParams();

        $namespace = isset($params['namespace']) ? $params['namespace'] : '';
        $noNeedAddQtyPrepare = [
            'os_needship_listing'
        ];

        if ($moduleName != 'fulfilsuccess' &&
            $moduleName != 'dropship' &&
            $moduleName != 'ordersuccess' &&
            ($moduleName . '_' . $controllerName . '_' . $actionName != 'sales_order_view') &&
            !($namespace && in_array($namespace, $noNeedAddQtyPrepare))
        ) {
            return true;
        }
        return false;
    }
}
