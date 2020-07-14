<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest;

use Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest\Collection;

/**
 * Class RemoveBatch
 * @package Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest
 */
class CancelBatch extends \Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest\AddToBatch
{

    /**
     * Add remove orders from Batch
     * 
     */
    public function execute()
    {
        $batchId = $this->getRequest()->getParam('batch_id');
        $position = $this->getRequest()->getParam('position');
        if($batchId) {
            $this->batchService->cancelBatch($batchId);
            $this->messageManager
                  ->addSuccessMessage(__('The batch has been removed'));
        } else {
            $this->messageManager
                 ->addWarningMessage(__('Can not remove the batch'));
        }
        $this->_redirect('fulfilsuccess/pickRequest');
    }
}