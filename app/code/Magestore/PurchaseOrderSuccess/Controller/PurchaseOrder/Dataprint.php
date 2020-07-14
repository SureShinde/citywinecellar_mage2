<?php

namespace Magestore\PurchaseOrderSuccess\Controller\PurchaseOrder;

use Magento\Framework\App\Filesystem\DirectoryList;

class Dataprint extends \Magestore\PurchaseOrderSuccess\Controller\AbstractController
{
    public function execute()
    {
        $purchaseKey = $this->getRequest()->getParam('key');
        $purchaseOrder = $this->purchaseOrderRepository->getByKey($purchaseKey);
        if ($purchaseOrder && $purchaseOrder->getPurchaseOrderId()) {
            $fileName = sprintf('purchase_order_%s.pdf', $purchaseOrder->getPurchaseCode());
            $supplier = $this->supplierRepository->getById($purchaseOrder->getSupplierId());
            $html = '<div>';
            $html .= $this->_resultPageFactory->create()->getLayout()->getBlock('print-header')->toHtml();
            $html .= $this->_resultPageFactory->create()->getLayout()->getBlock('print-items')->toHtml();
            $html .= $this->_resultPageFactory->create()->getLayout()->getBlock('print-total')->toHtml();
            $html .= '</div>';
            return $this->getResponse()->setBody($html);
        }
    }
}