<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Rewardpoints\Model\Action\Spending;

/**
 * Action Spend Point for Order
 */
class Creditmemo extends \Magestore\Rewardpoints\Model\Action\AbstractAction implements
    \Magestore\Rewardpoints\Model\Action\InterfaceAction
{
    /**
     * @inheritDoc
     */
    public function getPointAmount()
    {
        $creditmemo = $this->getData('action_object');
        return (int)$creditmemo->getRefundSpentPoints();
    }

    /**
     * @inheritDoc
     */
    public function getActionLabel()
    {
        return __('Retrieve points spent on refunded order');
    }

    /**
     * @inheritDoc
     */
    public function getActionType()
    {
        return \Magestore\Rewardpoints\Model\Transaction::ACTION_TYPE_SPEND;
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        $creditmemo = $this->getData('action_object');
        $order      = $creditmemo->getOrder();
        return __('Retrieve points spent on refunded order #%1', $order->getIncrementId());
    }

    /**
     * @inheritDoc
     */
    public function getTitleHtml($transaction = null)
    {
        if ($transaction === null) {
            return $this->getTitle();
        }
        if ($this->_storeManager->getStore()->getCode() == \Magento\Store\Model\Store::ADMIN_CODE) {
            $editUrl = $this->_urlBuilder->getUrl(
                'adminhtml/sales_order/view',
                ['order_id' => $transaction->getOrderId()]
            );
        } else {
            $editUrl = $this->_urlBuilder->getUrl(
                'sales/order/view',
                ['order_id' => $transaction->getOrderId()]
            );
        }
        return __(
            'Retrieve points spent on refunded order %1',
            '<a href="' . $editUrl .'" title="'
            . __('View Order')
            . '">#' . $transaction->getOrderIncrementId() . '</a>'
        );
    }

    /**
     * @inheritDoc
     */
    public function prepareTransaction()
    {
        $creditmemo = $this->getData('action_object');
        $order      = $creditmemo->getOrder();
        
        $transactionData = [
            'status'    => \Magestore\Rewardpoints\Model\Transaction::STATUS_COMPLETED,
            'order_id'  => $order->getId(),
            'order_increment_id'    => $order->getIncrementId(),
            'order_base_amount'     => $order->getBaseGrandTotal(),
            'order_amount'          => $order->getGrandTotal(),
            'base_discount'         => $creditmemo->getRewardpointsBaseDiscount(),
            'discount'              => $creditmemo->getRewardpointsDiscount(),
            'store_id'      => $order->getStoreId(),
            'extra_content' => $creditmemo->getIncrementId(),
        ];
        
        // Set expire time for current transaction
        $expireDays = (int)$this->_helper->getConfig(
            \Magestore\Rewardpoints\Helper\Calculation\Earning::XML_PATH_EARNING_EXPIRE,
            $order->getStoreId()
        );
        $transactionData['expiration_date'] = $this->getExpirationDate($expireDays);
        
        $this->setData('transaction_data', $transactionData);
        return parent::prepareTransaction();
    }
}
