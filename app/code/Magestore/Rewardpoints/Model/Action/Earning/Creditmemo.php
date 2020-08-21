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
namespace Magestore\Rewardpoints\Model\Action\Earning;

/**
 * Action Earn Point for Order
 */
class Creditmemo extends \Magestore\Rewardpoints\Model\Action\AbstractAction implements
    \Magestore\Rewardpoints\Model\Action\InterfaceAction
{
    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $_logger;

    /**
     * Cancel constructor.
     * @param \Magestore\Rewardpoints\Helper\Data $helper
     * @param \Magestore\Rewardpoints\Model\TransactionFactory $transaction
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Logger\Monolog $monolog
     */
    public function __construct(
        \Magestore\Rewardpoints\Helper\Data $helper,
        \Magestore\Rewardpoints\Model\TransactionFactory $transaction,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Logger\Monolog $monolog
    ) {
        parent::__construct($helper, $transaction, $storeManager, $urlBuilder);
        $this->_logger = $monolog;
    }

    /**
     * @inheritDoc
     */
    public function getPointAmount()
    {
        $creditmemo = $this->getData('action_object');
        return -(int)$creditmemo->getRefundEarnedPoints();
    }
    
    /**
     * @inheritDoc
     */
    public function getActionLabel()
    {
        return __('Taken back points for refunding order');
    }

    /**
     * @inheritDoc
     */
    public function getActionType()
    {
        return \Magestore\Rewardpoints\Model\Transaction::ACTION_TYPE_EARN;
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        $creditmemo = $this->getData('action_object');
        $order      = $creditmemo->getOrder();
        return __('Taken back points for refunding order #%1', $order->getIncrementId());
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
            'Taken back points for refunding order %1',
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
            'creditmemo_transaction' => true
        ];
        
        // Check all earning transaction is holding
        $earningTransactions = $this->_transaction->create()->getCollection()
            ->addFieldToFilter('action', 'earning_invoice')
            ->addFieldToFilter('order_id', $order->getId());
        $holdingAll = true;
        foreach ($earningTransactions as $transaction) {
            if ($transaction->getStatus() != \Magestore\Rewardpoints\Model\Transaction::STATUS_ON_HOLD) {
                $holdingAll = false;
                break;
            }
        }
        if ($holdingAll) {
            $transactionData['status'] = \Magestore\Rewardpoints\Model\Transaction::STATUS_ON_HOLD;
            $transactionData['creditmemo_holding'] = true;
        } else {
            // Complete holding transaction before refund
            foreach ($earningTransactions as $transaction) {
                if ($transaction->getStatus() != \Magestore\Rewardpoints\Model\Transaction::STATUS_ON_HOLD) {
                    continue;
                }
                try {
                    $transaction->completeTransaction();
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                }
            }
        }
        
        $this->setData('transaction_data', $transactionData);
        return parent::prepareTransaction();
    }
}
