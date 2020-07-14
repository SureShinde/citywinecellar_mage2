<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Plugin\Sales\Model\Order;

class Payment {
    public function afterPrependMessage(
        \Magento\Sales\Model\Order\Payment $subject,
        $result,
        $messagePrependTo
    ) {
        $registry = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\Registry');
        $newMessage = null;
        if($registry->registry('create_creditmemo_webpos') && strpos($messagePrependTo, 'We refunded') === 0) {
            $newMessage = str_replace('offline.', 'on POS system.', $messagePrependTo);
        }
        if($newMessage) {
            return $newMessage;
        } else {
            return $result;
        }
    }
}