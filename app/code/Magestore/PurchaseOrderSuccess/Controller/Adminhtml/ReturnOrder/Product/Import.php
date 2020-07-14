<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\Product;

use Magento\Framework\Message\MessageInterface;

/**
 * Class DownloadSample
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\Product
 */
class Import extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\AbstractAction
{
    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ImportService
     */
    protected $importService;

    protected $supplierProductService;

    public function execute() {
        $this->initVariable();

        $params = $this->getRequest()->getParams();
        try {
            $returnOrder = $this->returnOrderRepository->get($params['return_id']);
            $success = $this->importService->import(
                $this->getRequest()->getFiles('import_product'), $params['return_id'], $params['supplier_id']
            );
        }catch (\Exception $e){
            return $this->redirectForm(
                $params['return_id'],
                $e->getMessage(),
                MessageInterface::TYPE_ERROR
            );
        }
        if($success>0){
            return $this->redirectForm(
                $params['return_id'],  __('%1 item has been imported.', $success)
            );
        }
        return $this->redirectForm(
            $params['return_id'],
            __('No item has been imported.'),
            MessageInterface::TYPE_WARNING
        );
    }

    private function initVariable() {
        $this->importService = $this->_objectManager->get('Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ImportService');
        $this->supplierProductService = $this->_objectManager->get('Magestore\SupplierSuccess\Service\Supplier\ProductService');
    }
}