<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Template;

/**
 * Class Delete
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractTemplate
 */
class Delete extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractTemplate
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $path = '*/*/';
        $resultRedirect = $this->resultRedirectFactory->create();
        try{
            $id = $this->getRequest()->getParam('id');
            if(isset($id) && !empty($id)){
                $model = $this->helper->getModel('Magestore\BarcodeSuccess\Model\Template');
                $model->setTemplateId($id);
                $this->resource->delete($model);
            }
            $this->messageManager->addSuccessMessage(__('The template has been deleted successfully'));
        }catch (\Exception $ex){
            $this->messageManager->addErrorMessage($ex->getMessage());
        }
        return $resultRedirect->setPath($path);
    }
}
