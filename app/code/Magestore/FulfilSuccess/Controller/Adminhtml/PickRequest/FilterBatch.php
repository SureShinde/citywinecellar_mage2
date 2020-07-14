<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest;

use Magestore\FulfilSuccess\Service\Locator\BatchServiceInterface;

class FilterBatch extends \Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest
{
    /**
     * Filter pick requests by batch
     * 
     */
    public function execute()
    {
        $batchId = $this->_request->getParam('batch_id');
        
        $this->_session->setData(BatchServiceInterface::CURRENT_BATCH_SESSION_ID, $batchId);
        
        $this->_redirect('*/*/index');
    }
}