<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Index;

use Magestore\BarcodeSuccess\Model\History;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

/**
 * Class MassGenerate
 *
 * Used to mass generate
 */
class MassGenerate extends \Magestore\BarcodeSuccess\Controller\Adminhtml\Index\Save implements
    HttpPostActionInterface
{
    /**
     * Execute
     *
     * @return mixed
     */
    public function execute()
    {
        $path = 'catalog/product/';
        $resultRedirect = $this->resultRedirectFactory->create();
        $source = $this->getRequest()->getParam('source');
        $selected = $this->getRequest()->getParam(Filter::SELECTED_PARAM);
        if (isset($source) && $source == 'product_listing') {
            try {
                $barcodes = [];
                $totalQty = 1;
                foreach ($selected as $productId) {
                    $productModel = $this->_objectManager->create(\Magento\Catalog\Model\Product::class)
                        ->load($productId);
                    if ($productModel->getId()) {
                        $barcodes[] = [
                            'product_id' => $productId,
                            'qty' => 1,
                            'product_sku' => $productModel->getData('sku'),
                            'supplier_code' => ''
                        ];
                    }
                    $totalQty++;
                }

                $historyId = $this->saveHistory($totalQty, History::GENERATED, '');
                $result = $this->generateTypeItem($barcodes, $historyId);
                if (count($result) > 0) {
                    if (isset($result['success'])) {
                        $this->messageManager->addSuccessMessage(
                            __("%1 barcode(s) has been generated.", count($result['success']))
                        );
                    } else {
                        $this->removeHistory($historyId);
                        $path = 'catalog/product/';
                    }
                    if (isset($result['fail'])) {
                        $this->messageManager->addErrorMessage(
                            __('Cannot generate %1 barcode(s), please change Barcode Pattern from the configuration'
                                .' to increase the maximum barcode number', count($result['fail']))
                        );
                    }
                }

            } catch (\Exception $ex) {
                $this->messageManager->addErrorMessage($ex->getMessage());
            }
        }
        return $resultRedirect->setPath($path);
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
