<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Controller\Adminhtml\AdjustStock;

use Magestore\AdjustStock\Api\Data\AdjustStock\AdjustStockInterface;

/**
 * Class Save
 * @package Magestore\AdjustStock\Controller\Adminhtml\AdjustStock
 */
class Save extends \Magestore\AdjustStock\Controller\Adminhtml\AdjustStock\AdjustStock
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $adjustStock = $this->adjustStockFactory->create();
                $adjustData = $this->getAdjustData($data);

                if(!$this->adjustStockManagement->checkAdjustmentCode(
                    isset($data[AdjustStockInterface::ADJUSTSTOCK_ID]) ? $data[AdjustStockInterface::ADJUSTSTOCK_ID] : null,
                    $adjustData[AdjustStockInterface::ADJUSTSTOCK_CODE])
                ) {
                    $this->messageManager->addErrorMessage(__('This code has been applied for another adjustment. Please use another code.'));
                    $this->_getSession()->setFormData($data);
                    if (isset($data['adjuststock_id'])) {
                        return $resultRedirect->setPath('*/*/edit', ['id' => $data['adjuststock_id']]);
                    }
                    return $resultRedirect->setPath('*/*/new');
                }

                if (isset($data[AdjustStockInterface::ADJUSTSTOCK_ID])) {
                    $adjustStock = $adjustStock->load($data[AdjustStockInterface::ADJUSTSTOCK_ID]);
                }
                $adjustData['products'] = [];
                if (isset($data['links'])) {
                    if (is_string($data['links'])) {
                        $data['links'] = json_decode($data['links'], true);
                    }
                    $adjustData['products'] = $this->getProducts($data['links']);
                }
                $this->adjustStockManagement->createAdjustment($adjustStock, $adjustData);
                /* if created adjuststock then complete it */
                if ($adjustStock->getId()) {
                    if ($this->getRequest()->getParam('back') == 'apply') {
                        if (count($adjustData['products']) <= 0) {
                            $this->messageManager->addErrorMessage(__('No product to adjust stock.'));
                            return $resultRedirect->setPath('*/*/edit', ['id' => $adjustStock->getId()]);
                        }
                        $this->adjustStockManagement->complete($adjustStock);
                        $this->messageManager->addSuccessMessage(__('The stock adjustment has been successfully applied.'));
                        return $resultRedirect->setPath('*/*/edit', ['id' => $adjustStock->getId()]);
                    }
                    $this->messageManager->addSuccessMessage(__('The adjustment has been saved.'));
                    if ($this->getRequest()->getParam('back') == 'edit') {
                        return $resultRedirect->setPath('*/*/edit', ['id' => $adjustStock->getId()]);
                    }
                }

                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back') == 'new') {
                    return $resultRedirect->setPath('*/*/new');
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Cannot save adjustment!'));
                $this->_getSession()->setFormData($data);
                if (isset($data['adjuststock_id'])) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $data['adjuststock_id']]);
                }
                return $resultRedirect->setPath('*/*/new');
            }
        }
        $this->messageManager->addErrorMessage(
            __('Unable to find adjust stock to create')
        );
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * get products to adjust stock
     *
     * @param array
     * @return array
     */
    public function getProducts($dataLinks)
    {
        $products = [];
        if (isset($dataLinks['product_list'])) {
            foreach ($dataLinks['product_list'] as $product) {
                $products[$product['id']] = [
                    'product_id' => $product['id'],
                    'product_sku' => $product['sku'],
                    'product_name' => $product['name'],
                    'old_qty' => $product['total_qty'],
                    'change_qty' => $product['change_qty'],
                    'new_qty' => $product['new_qty'],
                    'barcode' => $product['barcode']
                ];
            }
        }
        return $products;
    }

    /**
     * get adjust stock data
     *
     * @param array
     * @return array
     */
    public function getAdjustData($data)
    {
        $adjustData = [];
        $adjustData[AdjustStockInterface::ADJUSTSTOCK_CODE] = isset($data[AdjustStockInterface::ADJUSTSTOCK_CODE]) ?
            $data[AdjustStockInterface::ADJUSTSTOCK_CODE] :
            null;
        $adjustData[AdjustStockInterface::SOURCE_CODE] = isset($data[AdjustStockInterface::SOURCE_CODE]) ?
            $data[AdjustStockInterface::SOURCE_CODE] :
            null;
        $adjustData[AdjustStockInterface::SOURCE_NAME] = isset($data[AdjustStockInterface::SOURCE_NAME]) ?
            $data[AdjustStockInterface::SOURCE_NAME] :
            null;
        $adjustData[AdjustStockInterface::REASON] = isset($data[AdjustStockInterface::REASON]) ?
            $data[AdjustStockInterface::REASON] :
            '';

        return $adjustData;
    }

}
