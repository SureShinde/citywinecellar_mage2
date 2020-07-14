<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Order\Creditmemo;

use Magestore\Giftvoucher\Helper\Data;
use Magestore\Giftvoucher\Model\Giftvoucher;

/**
 * Adminhtml Giftvoucher Creditmemo ViewTotals Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class ViewTotals extends \Magento\Sales\Block\Adminhtml\Totals
{

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_dataObject;

    /**
     * @var Data
     */
    protected $giftcardHelper;

    /**
     * @var \Magestore\Giftvoucher\Model\History
     */
    protected $history;



    /**
     * ViewTotals constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Data $giftcardHelper
     * @param \Magestore\Giftvoucher\Model\History $history
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Framework\DataObject $dataObject
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Giftvoucher\Helper\Data $giftcardHelper,
        \Magestore\Giftvoucher\Model\History $history,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Framework\DataObject $dataObject,
        array $data = []
    ) {
        $this->_dataObject = $dataObject;
        $this->giftcardHelper = $giftcardHelper;
        $this->history = $history;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    public function initTotals()
    {
        $totalsBlock = $this->getParentBlock();
        $creditmemo = $totalsBlock->getCreditmemo();
        if ($creditmemo->getGiftVoucherDiscount() && $creditmemo->getGiftVoucherDiscount() > 0) {
            $orderIncrementId = $creditmemo->getOrder()->getIncrementId();
            $historyRefunds = $this->history->getCollection()->joinGiftcodeForGrid()
                ->addFieldToFilter('order_increment_id',$orderIncrementId)
                ->addFieldToFilter('action',\Magestore\Giftvoucher\Model\Actions::ACTIONS_REFUND)
                ->addFieldToFilter('creditmemo_increment_id',$creditmemo->getId());
            foreach ($historyRefunds as $index => $code) {
                if($code){
                    $totalsBlock->addTotal(new \Magento\Framework\DataObject(
                        [
                            'code' => 'giftvoucher_'.$index,
                            'label' => __('Gift Card (%1)', $this->giftcardHelper->getHiddenCode($code->getGiftCode())),
                            'value' => -$code->getAmount(),
                            'base_value' => $code->getAmount(),
                        ]
                    ), 'subtotal');

                    $totalsBlock->addTotal(new \Magento\Framework\DataObject(
                        [
                            'code' => 'giftvoucher_'.$index.'_',
                            'label' => __('Refunded To Gift Card (%1)', $this->giftcardHelper->getHiddenCode($code->getGiftCode())),
                            'value' => $code->getAmount(),
                            'area' => 'footer',
                        ]
                    ), 'grand_total');
                }
            }
            /*
            $totalsBlock->addTotal(new \Magento\Framework\DataObject(
                [
                    'code' => 'giftvoucher',
                    'label' => __('Gift Card (%1)', $this->giftcardHelper
                                                ->getHiddenCode($creditmemo->getOrder()->getGiftVoucherGiftCodes())),
                    'value' => -$creditmemo->getGiftVoucherDiscount(),
                    'base_value' => -$creditmemo->getBaseGiftVoucherDiscount(),
                ]
            ), 'subtotal');
            $listCodesDiscount = $creditmemo->getOrder()->getGiftVoucherGiftCodes();
            $listCodesDiscount = explode(',',$listCodesDiscount);
            $listCodeAmountDiscount = $creditmemo->getOrder()->getCodesDiscount();
            $listCodeAmountDiscount = explode(',',$listCodeAmountDiscount);
            $listCodeBaseAmountDiscount = $creditmemo->getOrder()->getCodesBaseDiscount();
            $listCodeBaseAmountDiscount = explode(',',$listCodeBaseAmountDiscount);
            foreach($listCodesDiscount as $index => $code){
                if($code){
                    $totalsBlock->addTotal(new \Magento\Framework\DataObject(
                        [
                            'code' => 'giftvoucher_'.$index,
                            'label' => __('Gift Card (%1)', $this->giftcardHelper->getHiddenCode($code)),
                            'value' => -$listCodeAmountDiscount[$index],
                            'base_value' => $listCodeBaseAmountDiscount[$index],
                        ]
                    ), 'subtotal');
                }
            }
            */
            /**
             * Get total discount and re-calculate discount value to showing
             */
            $discountTotal = $totalsBlock->getTotal('discount');
            if (!empty($discountTotal) && $discountTotal->getValue() != 0) {
                $discountTotal->setValue($discountTotal->getValue() + $creditmemo->getGiftVoucherDiscount());
                if ($discountTotal->getValue() != 0) {
                    $totalsBlock->addTotal($discountTotal);
                } else {
                    $totalsBlock->removeTotal($discountTotal->getCode());
                }
            }
        }
        /*
        $refund = (float)$creditmemo->getGiftcardRefundAmount();
        if (($refund >0 || $refund === 0.0) && ($creditmemo->getOrder()->getUseGiftCreditAmount()
            || $creditmemo->getOrder()->getGiftVoucherDiscount())) {
                $label = __('Refund to customer gift card code used to check out');
            $dataObject = $this->_dataObject->setData(
                [
                    'code' => 'giftcard_refund',
                    'label' => $label,
                    'value' => $refund,
                    'area' => 'footer',
                ]
            );
            $totalsBlock->addTotal($dataObject, 'subtotal');
        }
        */
    }
}
