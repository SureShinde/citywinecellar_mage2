<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Sales\Order\Creditmemo;

/**
 * Class InvoiceService
 */
class EmailSender extends \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender
{

    /**
     * @param $email
     * @param $name
     */
    public function setRecepient($email, $name) {
        $this->identityContainer->setCustomerEmail($email);
        $this->identityContainer->setCustomerName($name);
    }

    /**
     * @param Order $order
     * @return string
     */
    public function getPaymentEmailHtml($order)
    {
        return $this->getPaymentHtml($order);
    }

    public function sendCreditmemoToAnotherEmail($creditmemo, $email) {
        if (!$this->globalConfig->getValue('sales_email/general/async_sending') || $forceSyncMode) {
            $order = $creditmemo->getOrder();
            $transport = [
                'order' => $order,
                'creditmemo' => $creditmemo,
                'comment' => $creditmemo->getCustomerNoteNotify() ? $creditmemo->getCustomerNote() : '',
                'billing' => $order->getBillingAddress(),
                'payment_html' => $this->getPaymentEmailHtml($order),
                'store' => $order->getStore(),
                'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
                'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
            ];

            $this->templateContainer->setTemplateVars($transport);
            $order->setCustomerEmail($email);
            if ($this->checkAndSend($order)) {
                return true;
            }
        }
        return false;
    }
}