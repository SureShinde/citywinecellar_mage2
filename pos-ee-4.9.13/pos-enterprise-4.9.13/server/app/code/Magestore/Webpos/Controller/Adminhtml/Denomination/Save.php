<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Denomination;

/**
 * Class Save
 * @package Magestore\Webpos\Controller\Adminhtml\Denomination
 */
class Save extends \Magestore\Webpos\Controller\Adminhtml\Denomination\AbstractDenomination
{

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $modelId = (int)$this->getRequest()->getParam('denomination_id');
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }
        if ($modelId) {
            $model = $this->denominationFactory->create()
                ->load($modelId);
        } else {
            $model = $this->denominationFactory->create();
        }
        $model->setData($data);
        try {
            $this->denominationRepository->save($model);
            $this->messageManager->addSuccessMessage(__('You saved the denomination'));
        }catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return  $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }

        if ($this->getRequest()->getParam('back') == 'edit') {
            return  $resultRedirect->setPath('*/*/edit', ['id' =>$model->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }


}