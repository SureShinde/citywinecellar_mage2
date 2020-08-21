<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;

use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Class \Magestore\Webpos\Controller\Adminhtml\Pos\ForceSignOut
 *
 * Force Sign-out Pos
 * Methods:
 *  execute
 */
class ForceSignOut extends \Magestore\Webpos\Controller\Adminhtml\Pos\AbstractAction implements HttpGetActionInterface
{
    /**
     * Execute
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $modelId = $this->getRequest()->getParam('id');
        if ($modelId > 0) {
            $model = $this->posInterfaceFactory->create()->load($modelId);

            // dispatch event to logout POS
            $this->dispatchService->dispatchEventForceSignOut($model->getStaffId(), $model->getPosId());

            $model->setStaffId(null);
            $this->posRepository->save($model);
            $this->sessionRepository->signOutPos($modelId);
        }
        return $resultRedirect->setPath('*/*/edit', ['id' =>$modelId]);
    }
}
