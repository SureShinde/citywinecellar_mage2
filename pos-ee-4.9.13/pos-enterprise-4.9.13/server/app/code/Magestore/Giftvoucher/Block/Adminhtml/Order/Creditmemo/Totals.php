<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Order\Creditmemo;

/**
 * Class Totals
 * @package Magestore\Giftvoucher\Block\Adminhtml\Order\Creditmemo
 */
class Totals extends \Magento\Sales\Block\Adminhtml\Totals
{
    public function initTotals()
    {
        $totalsBlock = $this->getParentBlock();
        $creditmemo = $totalsBlock->getCreditmemo();
        $giftVoucherDiscount = $creditmemo->getGiftVoucherDiscount();

        if ($giftVoucherDiscount && $giftVoucherDiscount > 0) {

            $giftcardHelper = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magestore\Giftvoucher\Helper\Data');

            $dataObject = new \Magento\Framework\DataObject(
                [
                    'code' => 'giftvoucher',
                    'label' => __('Gift Card (%1)',
                                    $giftcardHelper->getHiddenCode($creditmemo->getOrder()->getGiftVoucherGiftCodes())),
                    'value' => -$giftVoucherDiscount,
                    'base_value' => -$creditmemo->getBaseGiftVoucherDiscount(),
                ]
            );
            $totalsBlock->addTotal($dataObject, 'subtotal');

            /*
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
                            'label' => __('Gift Card (%1)', $giftcardHelper->getHiddenCode($code)),
                            'value' => -$listCodeAmountDiscount[$index],
                            'base_value' => -$listCodeBaseAmountDiscount[$index],
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
                $discountTotal->setValue($discountTotal->getValue() + $giftVoucherDiscount);
                if ($discountTotal->getValue() != 0) {
                    $totalsBlock->addTotal($discountTotal);
                } else {
                    $totalsBlock->removeTotal($discountTotal->getCode());
                }
            }
        }
    }
}
