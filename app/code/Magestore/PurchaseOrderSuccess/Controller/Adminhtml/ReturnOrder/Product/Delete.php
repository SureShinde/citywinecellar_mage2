<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\Product;

/**
 * Class Save
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\Product
 */
class Delete extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\AbstractAction
{
    protected $resultRawFactory;

    public function execute() {
        $this->initVariable();

        $resultRaw = $this->resultRawFactory->create();
        try{
            $this->returnItemRepository->deleteById($this->getRequest()->getParam('id'));
            $resultRaw->setContents($this->getRequest()->getParam('product_id'));
        }catch (\Exception $e){
            $resultRaw->setContents(0);
        }
        return $resultRaw;
    }

    private function initVariable() {
        $this->resultRawFactory = $this->_objectManager->get('Magento\Framework\Controller\Result\RawFactory');
    }
}