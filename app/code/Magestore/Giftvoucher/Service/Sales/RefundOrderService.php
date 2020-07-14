<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Service\Sales;

use \Magestore\Giftvoucher\Model\Actions as GiftvoucherHistoryAction;

/**
 * process cancel gift card item
 *
 */
class RefundOrderService implements \Magestore\Giftvoucher\Api\Sales\RefundOrderServiceInterface
{
    /**
     * @var string
     */
    protected $process = 'create_creditmemo';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magestore\Giftvoucher\Api\GiftCode\GiftCodeManagementServiceInterface
     */
    protected $giftCodeManagementService;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\CreditHistory\CollectionFactory
     */
    protected $creditHistoryCollectionFactory;

    /**
     * @var \Magestore\Giftvoucher\Model\HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $quoteSession;

    /**
     * @var \Magestore\Giftvoucher\Model\GiftvoucherFactory
     */
    protected $giftvoucherFactory;

    /**
     * @var \Magestore\Giftvoucher\Helper\System
     */
    protected $helperSystem;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $currencyHelper;


    /**
     * RefundOrderService constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\State $state
     * @param \Magestore\Giftvoucher\Helper\Data $helperData
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Giftvoucher\Model\ResourceModel\CreditHistory\CollectionFactory $collectionFactory
     * @param \Magestore\Giftvoucher\Model\HistoryFactory $historyFactory
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory
     * @param \Magento\Backend\Model\Session\Quote $quoteSession
     * @param \Magestore\Giftvoucher\Helper\System $helperSystem
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Directory\Helper\Data $currencyHelper
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $state,
        \Magestore\Giftvoucher\Helper\Data $helperData,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Giftvoucher\Model\ResourceModel\CreditHistory\CollectionFactory $collectionFactory,
        \Magestore\Giftvoucher\Model\HistoryFactory $historyFactory,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory,
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \Magestore\Giftvoucher\Helper\System $helperSystem,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $currencyHelper
    )
    {
        $this->objectManager = $objectManager;
        $this->appState = $state;
        $this->helperData = $helperData;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        $this->creditHistoryCollectionFactory = $collectionFactory;
        $this->historyFactory = $historyFactory;
        $this->giftvoucherFactory = $giftvoucherFactory;
        $this->quoteSession = $quoteSession;
        $this->helperSystem = $helperSystem;
        $this->currencyFactory = $currencyFactory;
        $this->currencyHelper = $currencyHelper;
    }



    /**
     * return list applied gift discount for shipping
     * @param $baseTotal
     * @param $giftcodesApplied
     * @return array
     */
    public function getGiftcodeDiscountForShipping($baseTotal, $giftcodesApplied){

        if($giftcodesApplied){
            $giftcodesAppliedDiscountForShipping = \Zend_Json::decode($giftcodesApplied);
        }else{
            $giftcodesAppliedDiscountForShipping = [];
        }
        $total = 0;
        if(!empty($giftcodesAppliedDiscountForShipping)){
            foreach($giftcodesAppliedDiscountForShipping as $codeDiscount){
                $total += $codeDiscount['base_discount'];
            }
        }
        $remain_discount = $total;
        $result = [];
        if($baseTotal > 0 && $total > 0) {
            foreach ($giftcodesAppliedDiscountForShipping as $codeDiscount) {
                if($remain_discount <= 0 ){
                    continue;
                }
                $base_discount = $baseTotal * $codeDiscount['base_discount'] / $total;
                if($base_discount > $remain_discount){
                    $base_discount = $remain_discount;
                }
                $result[$codeDiscount['code']] = $this->priceCurrency->round($base_discount);
                $remain_discount -= $base_discount;
            }
        }

        return $result;
    }
    /**
     * Process refund amount to giftcode when refund items
     * @param $order
     * @param $creditmemo
     * @param $baseGiftvoucherDiscountTotal
     * @throws \Exception
     */
    public function processRefundAmountToGiftcode($order, $creditmemo, $baseGiftvoucherDiscountTotal)
    {

        $GiftcodesDiscountForShipping = [];
        if($baseTotal = $creditmemo->getBaseTotalGiftcodeDiscountAmountForShipping()){
            $giftcodesApplied = $order->getGiftcodesAppliedDiscountForShipping();
            $GiftcodesDiscountForShipping = $this->getGiftcodeDiscountForShipping($baseTotal, $giftcodesApplied);
        }

        if ($this->priceCurrency->round($baseGiftvoucherDiscountTotal) == 0) {
            return;
        }

        $listCodeAmountRefunded = array();
        foreach ($creditmemo->getItems() as $item) {
            if (($qtyRefund = $item->getQty()) > 0) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->getGiftcodesApplied() != '') {
                    $giftcodesAppliedItem = \Zend_Json::decode($orderItem->getGiftcodesApplied());
                    foreach ($giftcodesAppliedItem as $giftcode) {
                        $code = $giftcode['code'];
                        $baseGiftcodeDiscount = $giftcode['base_gift_card_discount_amount'];
                        $orderedQty = $giftcode['qty_ordered'];
                        $amountRefund = ($baseGiftcodeDiscount / $orderedQty * $qtyRefund);
                        if (isset($listCodeAmountRefunded[$code])) {
                            $listCodeAmountRefunded[$code] += $amountRefund;
                        } else {
                            $listCodeAmountRefunded[$code] = $amountRefund;
                        }
                    }
                }
            }
        }

        if ($codes = $order->getGiftVoucherGiftCodes()) {
            $codesArray = explode(',', $codes);
            foreach ($codesArray as $code) {
                if (!isset($listCodeAmountRefunded[$code])) {
                    continue;
                }
                $giftVoucher = $this->giftvoucherFactory->create()->loadByCode($code);
                $history = $this->historyFactory->create();

                $baseCurrencyCode = $order->getBaseCurrencyCode();
                $baseCurrency = $this->currencyFactory->create()->load($baseCurrencyCode);
                $currentCurrency = $this->currencyFactory->create()->load($giftVoucher->getData('currency'));

                $availableDiscount = $this->priceCurrency->round($listCodeAmountRefunded[$code]);
                if ($availableDiscount < $baseGiftvoucherDiscountTotal) {
                    $baseGiftvoucherDiscountTotal = $baseGiftvoucherDiscountTotal - $availableDiscount;
                } else {
                    $availableDiscount = $baseGiftvoucherDiscountTotal;
                    $baseGiftvoucherDiscountTotal = 0;
                }

                /* if have gift code discount for shipping */
                if (!empty($GiftcodesDiscountForShipping)){
                    if(isset($GiftcodesDiscountForShipping[$code])){
                        $availableDiscount += $GiftcodesDiscountForShipping[$code];
                    }
                }

                $discountRefund = $this->currencyHelper->currencyConvert($availableDiscount, $baseCurrencyCode, $giftVoucher->getData('currency'));
                $discountCurrentRefund = $this->currencyHelper->currencyConvert($availableDiscount, $baseCurrencyCode, $order->getOrderCurrencyCode());

                $balance = $giftVoucher->getBalance() + abs($discountRefund);
                $baseBalance = $balance * $balance / $baseCurrency->convert($balance, $currentCurrency);
                $currentBalance = $this->currencyHelper->currencyConvert($baseBalance, $baseCurrencyCode, $order->getOrderCurrencyCode());

                if ($giftVoucher->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_USED) {
                    $giftVoucher->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE);
                }
                $giftVoucher->setData('balance', $balance)->save();

                $action = GiftvoucherHistoryAction::ACTIONS_REFUND;
                $history->setData(array(
                    'order_increment_id' => $order->getIncrementId(),
                    'creditmemo_increment_id' => $creditmemo->getId(),
                    'giftvoucher_id' => $giftVoucher->getId(),
                    'created_at' => date("Y-m-d H:i:s"),
                    'action' => $action,
                    'amount' => $discountCurrentRefund,
                    'balance' => $currentBalance,
                    'currency' => $order->getOrderCurrencyCode(),
                    'status' => $giftVoucher->getStatus(),
                    'comments' => __('%1 Order %2', GiftvoucherHistoryAction::getActionLabel($action), $order->getIncrementId()),
                    'customer_id' => $order->getData('customer_id'),
                    'customer_email' => $order->getData('customer_email'),
                    'extra_content' => __('%1 by %2', GiftvoucherHistoryAction::getActionLabel($action), $this->helperSystem->getCurUser()->getUserName()),
                ))->save();
            }
        }

    }

    /**
     * Process cancel order applied gift card discount
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return boolean
     */
    public function execute($creditmemo)
    {
        if ($creditmemo->getBaseGiftVoucherDiscount()) {
            $this->refundOffline($creditmemo->getOrder(), $creditmemo->getBaseGiftVoucherDiscount(), null, $creditmemo);
        }
    }

    /**
     * Process refund giftcard discount
     *
     * @param \Magento\Sales\Model\Order $order
     * @param float $baseGiftvoucherDiscountTotal
     * @param string $action
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function refundOffline($order, $baseGiftvoucherDiscountTotal, $action = null, $creditmemo = null)
    {

        $action = $action ? $action : GiftvoucherHistoryAction::ACTIONS_REFUND;

        if ($creditmemo && $action == GiftvoucherHistoryAction::ACTIONS_REFUND) {
            $this->processRefundAmountToGiftcode($order, $creditmemo, $baseGiftvoucherDiscountTotal);
            return $this;
        }

        if ($codes = $order->getGiftVoucherGiftCodes()) {
            $codesArray = explode(',', $codes);
            foreach ($codesArray as $code) {
                if ($this->priceCurrency->round($baseGiftvoucherDiscountTotal) == 0) {
                    return;
                }
                $giftVoucher = $this->giftvoucherFactory->create()->loadByCode($code);
                $history = $this->historyFactory->create();
                $baseCurrency = $this->storeManager->getStore($order->getStoreId())->getBaseCurrency();
                $availableDiscount = 0;
                if ($rate = $baseCurrency->getRate($order->getOrderCurrencyCode())) {
                    $availableDiscount = ($history->getTotalSpent($giftVoucher, $order)
                            - $history->getTotalRefund($giftVoucher, $order)) / $rate;
                }
                if ($this->priceCurrency->round($availableDiscount) == 0) {
                    continue;
                }

                if ($availableDiscount < $baseGiftvoucherDiscountTotal) {
                    $baseGiftvoucherDiscountTotal = $baseGiftvoucherDiscountTotal - $availableDiscount;
                } else {
                    $availableDiscount = $baseGiftvoucherDiscountTotal;
                    $baseGiftvoucherDiscountTotal = 0;
                }

                $baseCurrencyCode = $order->getBaseCurrencyCode();
                $baseCurrency = $this->currencyFactory->create()->load($baseCurrencyCode);
                $currentCurrency = $this->currencyFactory->create()->load($giftVoucher->getData('currency'));

                $discountRefund = $this->currencyHelper->currencyConvert($availableDiscount, $baseCurrencyCode, $giftVoucher->getData('currency'));
                $discountCurrentRefund = $this->currencyHelper->currencyConvert($availableDiscount, $baseCurrencyCode, $order->getOrderCurrencyCode());

                $balance = $giftVoucher->getBalance() + abs($discountRefund);
                $baseBalance = $balance * $balance / $baseCurrency->convert($balance, $currentCurrency);
                $currentBalance = $this->currencyHelper->currencyConvert($baseBalance, $baseCurrencyCode, $order->getOrderCurrencyCode());

                if ($giftVoucher->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_USED) {
                    $giftVoucher->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE);
                }
                $giftVoucher->setData('balance', $balance)->save();

                $history->setData(array(
                    'order_increment_id' => $order->getIncrementId(),
                    'creditmemo_increment_id' => '',
                    'giftvoucher_id' => $giftVoucher->getId(),
                    'created_at' => date("Y-m-d H:i:s"),
                    'action' => $action,
                    'amount' => $discountCurrentRefund,
                    'balance' => $currentBalance,
                    'currency' => $order->getOrderCurrencyCode(),
                    'status' => $giftVoucher->getStatus(),
                    'comments' => __('%1 Order %2', GiftvoucherHistoryAction::getActionLabel($action), $order->getIncrementId()),
                    'customer_id' => $order->getData('customer_id'),
                    'customer_email' => $order->getData('customer_email'),
                    'extra_content' => __('%1 by %2', GiftvoucherHistoryAction::getActionLabel($action), $this->helperSystem->getCurUser()->getUserName()),
                ))->save();
            }
        }

        return $this;
    }

    /**
     * Check can refund order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return boolean
     */
    public function canRefund($order)
    {
        if ($order->isCanceled() || $order->getState() === \Magento\Sales\Model\Order::STATE_CLOSED) {
            return false;
        }
        if ($order->getBaseGrandTotal() == 0
            && $order->getBaseGiftVoucherDiscount() > 0
        ) {
            foreach ($order->getAllItems() as $item) {
                if ($item->canRefund()) {
                    return true;
                }
            }
        }
        return false;
    }
}
