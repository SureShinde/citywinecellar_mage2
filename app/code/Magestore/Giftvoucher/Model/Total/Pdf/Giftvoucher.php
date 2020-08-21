<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Total\Pdf;

/**
 * Giftvoucher Total Pdf Giftvoucher Model
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Giftvoucher extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{

    /**
     * @inheritDoc
     */
    public function getTotalsForDisplay()
    {

        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $totals = [];
        if (!$this->getSource() instanceof \Magento\Sales\Model\Order\Creditmemo) {
            $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
            if ($this->getAmountPrefix()) {
                $amount = $this->getAmountPrefix() . $amount;
            }
            $totals = [
                [
                    'label' => __('Gift Card (%1):', $this->getGiftCodes()),
                    'amount' => $amount,
                    'font_size' => $fontSize,
                ]
            ];
        }

        return $totals;
    }

    /**
     * @inheritDoc
     */
    public function getAmount()
    {
        if ($this->getSource()->getGiftVoucherDiscount()) {
            return -$this->getSource()->getGiftVoucherDiscount();
        }
        return 0;
    }

    /**
     * Get Gift Codes
     *
     * @return mixed
     */
    public function getGiftCodes()
    {
        $giftcardHelper = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magestore\Giftvoucher\Helper\Data::class);
        if ($this->getOrder()->getGiftVoucherGiftCodes()) {
            return $giftcardHelper->getHiddenCode($this->getOrder()->getGiftVoucherGiftCodes());
        }
        return $this->getOrder()->getGiftVoucherGiftCodes();
    }
}
