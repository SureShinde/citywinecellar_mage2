<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Order;

use Magestore\OrderSuccess\Api\Data\OrderPositionInterface;
use Magento\Sales\Model\Order as OrderInterface;
/**
 * Class View
 * @package Magestore\OrderSuccess\Block\Adminhtml\Sales
 */
class View extends \Magento\Sales\Block\Adminhtml\Order\View
{

    const SECTION_CONFIG_ORDER_VERIFY = 'ordersuccess/order/verify';
    const SECTION_CONFIG_ORDER_SHIP = 'ordersuccess/order/ship';

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $order = $this->getOrder();
        $this->initButtons($order);
    }

    /**
     * @param $order
     */
    public function initButtons($order){
        if ($this->_isAllowedAction('Magento_Sales::actions_edit') && $order->canEdit()) {
            $this->buttonList->update(
                'order_edit',
                'class',
                'edit'
            );
        }
        if ($this->_isAllowedAction('Magento_Sales::unhold') && $order->canUnhold()) {
            $this->buttonList->update(
                'order_unhold',
                'class',
                'unhold primary'
            );
        }
        if(!$this->useShip()) {
            $this->buttonList->remove('order_ship');
        }
        if ($this->_isAllowedAction('Magestore_OrderSuccess::need_to_verify')
            && !$order->getIsVerified() && $this->needVerify() && $this->canVerify($order)) {
            $orderPosition = $this->getRequest()->getParam('order_position')?:OrderPositionInterface::NEED_SHIP;
            $this->buttonList->add(
                'order_verify',
                [
                    'label' => __('Mark as Verified'),
                    'onclick' => 'setLocation(\'' . $this->getVerifyUrl($order->getId(), $orderPosition) . '\')',
                    'class' => 'ship primary'
                ],
                '1',
                '101'
            );
        }
        if ($this->_isAllowedAction('Magestore_OrderSuccess::need_to_ship')
            && ($order->getIsVerified() || !$this->needVerify())) {
            if($order->canShip() && !$order->getForcedShipmentWithInvoice()) {
                $orderId = $order->getId();
                $this->buttonList->add(
                    'order_ship_process',
                    [
                        'label' => __('Fulfill'),
                        'onclick' => 'packaging.showWindow('.$orderId.');',
                        'class' => 'verify primary'
                    ],
                    '1',
                    '100'
                );
            }
            if($this->needVerify()) {
                if ($order->getIsVerified() && $this->canVerify($order)) {
                    $orderPosition = $this->getRequest()->getParam('order_position') ?: OrderPositionInterface::NEED_SHIP;
                    if ($this->isBackOrder($order)) {
                        $this->buttonList->add(
                            'order_back_ship',
                            [
                                'label' => __('Back to ship'),
                                'onclick' => 'setLocation(\'' . $this->getNeedShipUrl($order->getId(), $orderPosition) . '\')',
                                'class' => 'back_verify'
                            ],
                            '1',
                            '99'
                        );
                    } else if($order->canShip()){
                        $orderPosition = $this->getRequest()->getParam('order_position') ?: OrderPositionInterface::NEED_VERIFY;
                        $this->buttonList->add(
                            'order_back_verify',
                            [
                                'label' => __('Back to Verify'),
                                'onclick' => 'setLocation(\'' . $this->getBackVerifyUrl($order->getId(), $orderPosition) . '\')',
                                'class' => 'back_verify'
                            ],
                            '1',
                            '99'
                        );
                    }
                }
            }
            if(!$this->needVerify()) {
                if ($this->isBackOrder($order)) {
                    if (isset($orderPosition)) {
                        $this->buttonList->add(
                            'order_back_ship',
                            [
                                'label' => __('Back to ship'),
                                'onclick' => 'setLocation(\'' . $this->getNeedShipUrl($order->getId(), $orderPosition) . '\')',
                                'class' => 'back_verify'
                            ],
                            '1',
                            '99'
                        );
                    }

                }
            }
        }

    }

    /**
     * Verify URL
     *
     * @return string
     */
    public function getVerifyUrl($orderId, $orderPosition)
    {
        return $this->getUrl('ordersuccess/order/verify',
            [
                'order_id' => $orderId,
                'order_position' => $orderPosition
            ]);
    }

    /**
     * Back to need to ship URL
     *
     * @return string
     */
    public function getNeedShipUrl($orderId, $orderPosition)
    {
        return $this->getUrl('ordersuccess/order/needShip',
            [
                'order_id' => $orderId,
                'order_position' => $orderPosition
            ]);
    }

    /**
     * Back to verify URL
     *
     * @return string
     */
    public function getBackVerifyUrl($orderId, $orderPosition)
    {
        return $this->getUrl('ordersuccess/order/backverify',
            [
                'order_id' => $orderId,
                'order_position' => $orderPosition
            ]);
    }

    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
        $orderPosition = $this->getRequest()->getParam('order_position')?:OrderPositionInterface::NEED_SHIP;
        return $this->getUrl('ordersuccess/'.$orderPosition.'/', ['order_position' => $orderPosition]);
    }

    /**
     * is back order
     *
     * @param array $order
     * @return boolean
     */
    public function isBackOrder($order)
    {
        foreach ($order->getAllItems() as $item) {
            if ($item->getQtyBackordered() > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Can use magento ship
     *
     * @return boolean
     */
    public function useShip()
    {
        return $this->_scopeConfig->getValue(self::SECTION_CONFIG_ORDER_SHIP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * need verify order before process
     *
     * @return boolean
     */
    public function needVerify()
    {
        return $this->_scopeConfig->getValue(self::SECTION_CONFIG_ORDER_VERIFY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * check order was verified
     *
     * @param OrderInterface
     * @return boolean
     */
    public function canVerify($order)
    {
        $statusArray = [
            OrderInterface::STATE_HOLDED,
            OrderInterface::STATE_CANCELED,
            OrderInterface::STATE_CLOSED,
            OrderInterface::STATE_COMPLETE
        ];
        if(in_array($order->getStatus(), $statusArray))
            return false;
        return true;
    }

    /**
     * get order step
     */
    public function getOrderStep($order)
    {
        if(!$order->getIsVerified() && $this->needVerify() && $this->canVerify($order)){
            return __('Need to verify');
        }
        if(($order->getIsVerified() || !$this->needVerify())) {
            if ($order->canShip() && !$order->getForcedShipmentWithInvoice()) {
                return __('Verified');
            }
        }
        return '';
    }

}

