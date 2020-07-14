<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder;

/**
 * Class View
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Quotation
 */
class View extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::view_return_order';

    /**
     * View return order form
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id', null);
        if($id){
            try{
                $returnOrder = $this->returnOrderRepository->get($id);
            }catch (\Exception $e){
                return $this->redirectGrid($e->getMessage());
            }
        }else{
            $returnOrder = $this->_returnOrderFactory->create();
        }
        $this->_registry->register('current_return_order', $returnOrder);
        $resultPage = $this->_initAction();
        if($id){
            $code = $returnOrder->getReturnCode();
            $code = $code?$code:$id;
            $resultPage->getConfig()->getTitle()->prepend(__('View Return Request #'.  $code));
        }else{
            $resultPage->getConfig()->getTitle()->prepend(__('New Return Request'));
        }
        return $resultPage;
    }
}