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
class Giftvoucher extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{

    /**
     * @return array
     */
    public function getTotalsForDisplay()
    {

        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $totals = array();
        if (  $this->getSource() instanceof \Magento\Sales\Model\Order\Creditmemo){
            /* not todo*/
        }else{
            $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
            if ($this->getAmountPrefix()) {
                $amount = $this->getAmountPrefix() . $amount;
            }
            $totals = array(array(
                'label' => __('Gift Card (%1):', $this->getGiftCodes()),
                'amount' => $amount,
                'font_size' => $fontSize,
            )
            );
        }

        return $totals;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        if ($this->getSource()->getGiftVoucherDiscount()) {
            return -$this->getSource()->getGiftVoucherDiscount();
        }
    }

    /**
     * @return mixed
     */
    public function getGiftCodes()
    {
        $giftcardHelper = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magestore\Giftvoucher\Helper\Data');
        if($this->getOrder()->getGiftVoucherGiftCodes()){
            return $giftcardHelper->getHiddenCode($this->getOrder()->getGiftVoucherGiftCodes());
        }
        return $this->getOrder()->getGiftVoucherGiftCodes();
    }
}
