<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest;

use Magestore\FulfilSuccess\Service\Location\LocationServiceInterface;

class ChangeWarehouse extends \Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest
{
    /**
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $warehouseId = $this->_request->getParam('warehouse_id');
        $this->_session->setData(LocationServiceInterface::CURRENT_WAREHOUSE_SESSION_ID, $warehouseId);
        $this->_redirect('*/*/index');
    }
}
