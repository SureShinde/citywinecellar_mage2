<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PaymentOffline\Plugin\Block\Payment\Method;

use Magento\Framework\Exception\LocalizedException;

/**
 * Plugin payment MultiPayment
 */
class MultiPayment extends \Magestore\Payment\Block\Payment\Method\MultiPayment
{
    /**
     * Around Get Specific Information
     *
     * @param \Magestore\Payment\Block\Payment\Method\MultiPayment $subject
     * @param callable $proceed
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetSpecificInformation(
        \Magestore\Payment\Block\Payment\Method\MultiPayment $subject,
        callable $proceed
    ) {
        $specificInformation = [];
        $actualTotalPaid = 0;

        foreach ($subject->getOrderPaymentMethods() as $paymentMethod) {
            if ($paymentMethod->getData('base_amount_paid') >= 0) {
                $actualTotalPaid += $paymentMethod->getData('base_amount_paid');
                $specificInformation[] = [
                    'label' => $paymentMethod->getData('title'),
                    'value' => ($paymentMethod->getData('base_amount_paid') > 0)
                        ? $this->_helperPricing->currency($paymentMethod->getData('base_amount_paid'), true, false)
                        : 'Pay Later',
                    'reference_number' => $paymentMethod->getData('reference_number'),
                    'card_type' => $paymentMethod->getData('card_type')
                ];
            }
        }
        $orderId = $subject->getInfo()->getData('parent_id');
        if ($subject->_coreRegistry->registry('current_order')) {
            $baseTotalPaid = $subject->_coreRegistry->registry('current_order')->getBaseTotalPaid();
        } else {
            try {
                $baseTotalPaid = $subject->_orderRepository->get($orderId)->getBaseTotalPaid();
            } catch (\Exception $e) {
                throw new LocalizedException(__($e->getMessage()));
            }
        }
        if ($baseTotalPaid !== 0) {
            if ($actualTotalPaid < $baseTotalPaid) {
                array_push($specificInformation, [
                    'label' => __('Other'),
                    'value' => $subject->_helperPricing->currency($baseTotalPaid - $actualTotalPaid, true, false),
                ]);
            }
        }
        return $specificInformation;
    }
}
