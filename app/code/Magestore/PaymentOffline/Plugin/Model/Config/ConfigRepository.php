<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PaymentOffline\Plugin\Model\Config;

/**
 * Class ConfigRepository
 * @package Magestore\PaymentOffline\Plugin\Model\Config
 */
class ConfigRepository extends \Magestore\Webpos\Model\Config\ConfigRepository
{
    /**
     * @param \Magestore\Webpos\Model\Config\ConfigRepository $subject
     * @param $refundPaymentType
     * @return mixed
     */
    public function afterGetRefundPaymentType(\Magestore\Webpos\Model\Config\ConfigRepository $subject, $refundPaymentType)
    {
        //Get Object Manager Instance
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \Magestore\PaymentOffline\Service\PaymentOfflineService $paymentOfflineService */
        $paymentOfflineService = $objectManager->create('Magestore\PaymentOffline\Service\PaymentOfflineService');
        $paymentOfflines = $paymentOfflineService->getAllPaymentOfflineAvailable();
        $acceptedPayment = $refundPaymentType->getAcceptedPayments();
        if (count($paymentOfflines)) {
            foreach ($paymentOfflines as $paymentOffline) {
                if (!$paymentOffline->getUsePayLater()) {
                    $acceptedPayment .= ',' . $paymentOffline->getPaymentCode();
                }
            }
        }
        $refundPaymentType->setAcceptedPayments($acceptedPayment);
        return $refundPaymentType;
    }

}
