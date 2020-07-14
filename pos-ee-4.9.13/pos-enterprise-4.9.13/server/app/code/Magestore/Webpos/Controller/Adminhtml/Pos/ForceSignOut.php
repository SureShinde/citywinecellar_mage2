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
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Pos
 * @module      Webpos
 * @author      Magestore Developer
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

            /* notify to admin when force sign out and do not force sign-out */
            if ($model->getStaffId() && $this->helper->isEnableSession()) {
                $currentShift = $this->shiftRepository->getCurrentShiftByPosId($model->getPosId());
                $staff = $this->staffRepository->getById($model->getStaffId());
                if ($staff->getName() && $currentShift->getId()) {
                    $this->messageManager->addWarningMessage(
                        __('POS is still working in the session. Please close current session!')
                    );
                    return $resultRedirect->setPath('*/*/edit', ['id' =>$modelId]);
                }
            }

            // dispatch event to logout POS
            $this->dispatchService->dispatchEventForceSignOut($model->getStaffId(), $model->getPosId());

            $model->setStaffId(null);
            $this->posRepository->save($model);
            $this->sessionRepository->signOutPos($modelId);
        }
        return $resultRedirect->setPath('*/*/edit', ['id' =>$modelId]);
    }
}
