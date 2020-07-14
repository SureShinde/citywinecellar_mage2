<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Email;

/**
 * Class TransportBuilder
 * @package Magestore\PurchaseOrderSuccess\Model\Email
 */
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    public function attachFile($file, $name) {
        if (!empty($file) && file_exists($file)) {
            $this->message
                ->createAttachment(
                    file_get_contents($file),
                    \Zend_Mime::TYPE_OCTETSTREAM,
                    \Zend_Mime::DISPOSITION_ATTACHMENT,
                    \Zend_Mime::ENCODING_BASE64,
                    basename($name)
                );
        }
        return $this;
    }
}