<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Template;

/**
 * Class MassDelete
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractTemplate
 */
class MassDelete extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractTemplate
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $path = '*/*/';
        $resultRedirect = $this->resultRedirectFactory->create();
        try{
            $excluded = $this->getRequest()->getParam('excluded');
            $selected = $this->getRequest()->getParam('selected');
            if(isset($excluded) && $excluded == 'false'){
                $model = $this->helper->getModel('Magestore\BarcodeSuccess\Model\Template');
                $collection = $model->getCollection();
                if($collection->getSize() > 0){
                    foreach ($collection as $model) {
                        $this->resource->delete($model);
                    }
                }
            }
            if(isset($selected) && !empty($selected)){
                $model = $this->helper->getModel('Magestore\BarcodeSuccess\Model\Template');
                $collection = $model->getCollection()->addFieldToFilter('template_id',['in' => $selected]);
                if($collection->getSize() > 0){
                    foreach ($collection as $model) {
                        $this->resource->delete($model);
                    }
                }
            }
            $this->messageManager->addSuccessMessage(__('The template(s) has been deleted successfully'));
        }catch (\Exception $ex){
            $this->messageManager->addErrorMessage($ex->getMessage());
        }
        return $resultRedirect->setPath($path);
    }
}
