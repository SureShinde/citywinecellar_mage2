<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Index;
use Magestore\BarcodeSuccess\Model\History;
use Magestore\BarcodeSuccess\Model\Source\GenerateType;

/**
 * Class Create
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\Index
 */
class Create extends \Magestore\BarcodeSuccess\Controller\Adminhtml\Index\Save
{

    /**
     * @return mixed
     */
    public function execute()
    {
        try{
            $barcodes = array();
            $totalQty = 1;
            $productId = $this->getRequest()->getParam('product_id');
            $productModel = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
            if ($productModel->getId()) {
                $barcodes[] = array(
                    'product_id' => $productId,
                    'qty' => 1,
                    'product_sku' => $productModel->getData('sku'),
                    'supplier_code' => ''
                );
                $historyId = $this->saveHistory($totalQty, History::GENERATED, '');
                $result = $this->generateTypeItem($barcodes, $historyId);
                if (isset($result['success']) && count($result['success'])) {
                    return $this->getResponse()->setBody(1);
                } else {
                    return $this->getResponse()->setBody(0);
                }
            }

        }catch (\Exception $ex){
            return $this->getResponse()->setBody(0);
        }
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
