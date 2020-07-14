<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Total\Pdf;

/**
 * Giftvoucher Total Pdf Giftvoucher Model
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class GiftvoucherRefund extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{

    /**
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $totals = array();
        if (  $this->getSource() instanceof \Magento\Sales\Model\Order\Creditmemo){
            $historyfactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magestore\Giftvoucher\Model\History');
            $giftcardHelper = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magestore\Giftvoucher\Helper\Data');

            $orderIncrementId = $this->getOrder()->getIncrementId();
            $historyRefunds = $historyfactory->getCollection()->joinGiftcodeForGrid()
                ->addFieldToFilter('order_increment_id',$orderIncrementId)
                ->addFieldToFilter('action',\Magestore\Giftvoucher\Model\Actions::ACTIONS_REFUND)
                ->addFieldToFilter('creditmemo_increment_id',$this->getSource()->getId());
            foreach ($historyRefunds as $index => $code) {
                if($code){
                    $amount = $this->getOrder()->formatPriceTxt($code->getAmount());
                    if ($this->getAmountPrefix()) {
                        $amount = $this->getAmountPrefix() . $amount;
                    }
                    $total = array(
                        'label' => __('Refunded To Gift Card (%1):', $giftcardHelper->getHiddenCode($code->getGiftCode())),
                        'amount' => $amount,
                        'font_size' => $fontSize,
                    );
                    $totals[] = $total;
                }
            }
        }
        return $totals;
    }
}
