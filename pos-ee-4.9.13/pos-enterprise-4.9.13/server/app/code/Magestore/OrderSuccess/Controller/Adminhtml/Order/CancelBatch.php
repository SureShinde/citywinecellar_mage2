<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class RemoveFromBatch
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Sales
 */
class CancelBatch extends \Magestore\OrderSuccess\Controller\Adminhtml\Order\OrderAction
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
            $batchIds = [$batchId];
            $this->batchService->cancelBatchs($batchIds);
            $this->messageManager
                  ->addSuccessMessage(__('The batch has been removed'));
        } else {
            $this->messageManager
                 ->addWarningMessage(__('Can not remove the batch'));
        }
        $this->_redirect('ordersuccess/'.$position);
    }
}