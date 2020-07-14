<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Order\Creditmemo;

/**
 * Giftvoucher Creditmemo Totals Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Totals extends \Magento\Sales\Block\Order\Totals
{

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_dataObject;

    /**
     * Totals constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\DataObject $dataObject
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\DataObject $dataObject,
        array $data = []
    ) {
        $this->_dataObject = $dataObject;
        parent::__construct($context, $registry, $data);
    }

    public function initTotals()
    {
        $totalsBlock = $this->getParentBlock();
        $creditmemo = $totalsBlock->getCreditmemo();
        if ($creditmemo->getGiftVoucherDiscount() && $creditmemo->getGiftVoucherDiscount() > 0) {

            $historyfactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magestore\Giftvoucher\Model\History');
            $giftcardHelper = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magestore\Giftvoucher\Helper\Data');
            $orderIncrementId = $creditmemo->getOrder()->getIncrementId();
            $historyRefunds = $historyfactory->getCollection()->joinGiftcodeForGrid()
                ->addFieldToFilter('order_increment_id',$orderIncrementId)
                ->addFieldToFilter('action',\Magestore\Giftvoucher\Model\Actions::ACTIONS_REFUND)
                ->addFieldToFilter('creditmemo_increment_id',$creditmemo->getId());
            foreach ($historyRefunds as $index => $code) {
                if($code){
                    $totalsBlock->addTotal(new \Magento\Framework\DataObject(
                        [
                            'code' => 'giftvoucher_'.$index,
                            'label' => __('Refunded To Gift Card (%1)', $giftcardHelper->getHiddenCode($code->getGiftCode())),
                            'value' => $code->getAmount(),
                            'area' => 'footer',
                        ]
                    ), 'grand_total');
                }
            }

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
        $refund = (float) $creditmemo->getGiftcardRefundAmount();
        if (($refund >0 || $refund === 0.0) && $creditmemo->getOrder()->getGiftVoucherDiscount()) {
            $label = __('Refund to your gift card code');
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
    }
}
