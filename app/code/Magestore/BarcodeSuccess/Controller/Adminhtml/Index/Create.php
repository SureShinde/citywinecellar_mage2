<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Index;

use Magestore\BarcodeSuccess\Model\History;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

/**
 * Class Create
 *
 * Used to create
 */
class Create extends \Magestore\BarcodeSuccess\Controller\Adminhtml\Index\Save implements HttpPostActionInterface
{

    /**
     * Execute create
     *
     * @return mixed
     */
    public function execute()
    {
        try {
            $barcodes = [];
            $totalQty = 1;
            $productId = $this->getRequest()->getParam('product_id');
            $productModel = $this->_objectManager->create(\Magento\Catalog\Model\Product::class)
                ->load($productId);
            if ($productModel->getId()) {
                $barcodes[] = [
                    'product_id' => $productId,
                    'qty' => 1,
                    'product_sku' => $productModel->getData('sku'),
                    'supplier_code' => ''
                ];
                $historyId = $this->saveHistory($totalQty, History::GENERATED, '');
                $result = $this->generateTypeItem($barcodes, $historyId);
                if (isset($result['success']) && count($result['success'])) {
                    return $this->getResponse()->setBody(1);
                } else {
                    return $this->getResponse()->setBody(0);
                }
            }
        } catch (\Exception $ex) {
            return $this->getResponse()->setBody(0);
        }
        return $this->getResponse()->setBody(1);
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_BarcodeSuccess::generate_barcode');
    }
}
