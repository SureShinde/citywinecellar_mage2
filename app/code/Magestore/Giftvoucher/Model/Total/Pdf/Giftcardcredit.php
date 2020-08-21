<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Total\Pdf;

/**
 * Giftvoucher Total Pdf Giftcardcredit Model
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Giftcardcredit extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{

    /**
     * @inheritDoc
     */
    public function getTotalsForDisplay()
    {

        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        if ($this->getAmountPrefix()) {
            $amount = $this->getAmountPrefix() . $amount;
        }
        $totals = [
            [
                'label' => __('Gift Card credit:'),
                'amount' => $amount,
                'font_size' => $fontSize,
            ]
        ];
        return $totals;
    }

    /**
     * @inheritDoc
     */
    public function getAmount()
    {
        if ($this->getSource()->getUseGiftCreditAmount()) {
            return -$this->getSource()->getUseGiftCreditAmount();
        }
        return -$this->getOrder()->getUseGiftCreditAmount();
    }
}
