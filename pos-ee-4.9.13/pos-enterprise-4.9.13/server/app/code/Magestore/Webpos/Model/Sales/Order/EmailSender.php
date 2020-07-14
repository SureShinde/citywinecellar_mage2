<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Sales\Order;

/**
 * Class InvoiceService
 */
class EmailSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{

    /**
     * @inheritdoc
     */
    public function setRecepient($email, $name) {
        $this->identityContainer->setCustomerEmail($email);
        $this->identityContainer->getCustomerName($name);
    }
}