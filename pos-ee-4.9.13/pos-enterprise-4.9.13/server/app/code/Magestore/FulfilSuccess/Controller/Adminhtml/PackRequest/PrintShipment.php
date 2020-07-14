<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PackRequest;

class PrintShipment extends \Magestore\FulfilSuccess\Controller\Adminhtml\PackRequest
{
    /**
     * PrinShipment action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $currenShipmentId = $this->_session->getData('current_shipment_id');
        $this->_session->setData('current_shipment_id', null);
        $this->_forward('print', 'shipment', 'sales', ['shipment_id' => $currenShipmentId]);
    }
}
