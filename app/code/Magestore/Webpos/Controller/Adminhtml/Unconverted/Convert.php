<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Controller\Adminhtml\Unconverted;

/**
 * Class \Magestore\Webpos\Controller\Adminhtml\Unconverted\Converted
 */
class Convert extends \Magestore\Webpos\Controller\Adminhtml\Unconverted\AbstractAction
{
    /**
     * Converted order
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $incrementId = $this->getRequest()->getParam('increment_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->posOrderRepository->processConvertOrder($incrementId);
            $this->messageManager->addSuccessMessage(__('Order were converted.'));
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}
