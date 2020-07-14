<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Template;

/**
 * Class MassStatus
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractTemplate
 */
class MassStatus extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractTemplate
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
            $status = $this->getRequest()->getParam('status');
            if(isset($excluded) && $excluded == 'false'){
                $model = $this->helper->getModel('Magestore\BarcodeSuccess\Model\Template');
                $collection = $model->getCollection();
                if($collection->getSize() > 0){
                    foreach ($collection as $model) {
                        $model->setData('status', $status);
                        $this->resource->save($model);
                    }
                }
            }
            if(isset($selected) && !empty($selected)){
                $model = $this->helper->getModel('Magestore\BarcodeSuccess\Model\Template');
                $collection = $model->getCollection()->addFieldToFilter('template_id',['in' => $selected]);
                if($collection->getSize() > 0){
                    foreach ($collection as $model) {
                        $model->setData('status', $status);
                        $this->resource->save($model);
                    }
                }
            }
            $this->messageManager->addSuccessMessage(__('The template(s) status has been saved successfully'));
        }catch (\Exception $ex){
            $this->messageManager->addErrorMessage($ex->getMessage());
        }
        return $resultRedirect->setPath($path);
    }
}
