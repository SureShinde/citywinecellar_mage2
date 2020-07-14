<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Controller\Adminhtml\AdjustStock;

use Magestore\AdjustStock\Api\Data\AdjustStock\AdjustStockInterface;

/**
 * Class Save
 * @package Magestore\AdjustStock\Controller\Adminhtml\AdjustStock
 */
class Delete extends \Magestore\AdjustStock\Controller\Adminhtml\AdjustStock\AdjustStock
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $adjustId = $this->getRequest()->getParam('id');
        if(!$adjustId) {
            return $resultRedirect->setPath('*/*/');
        }

        /** @var \Magestore\AdjustStock\Model\AdjustStock $adjustStock */
        $adjustStock = $this->adjustStockFactory->create();
        $adjustStock->load($adjustId);
        if(!$adjustStock->getId()) {
            $this->messageManager->addErrorMessage(__('Adjustment doesn\'t exist.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $adjustCode = $adjustStock->getAdjustStockCode();
            $adjustStock->getResource()->delete($adjustStock);

            $this->messageManager->addSuccessMessage(__('Stock Adjustment %1 has been deleted.', $adjustCode));
            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Cannot delete this adjustment!'));
            return $resultRedirect->setPath('*/*/');
        }
    }
}
