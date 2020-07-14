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
class Scan extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\AbstractAction
{
    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\ReturnOrder\ItemFactory
     */
    protected $returnProductFactory;

    public function execute() {
        $this->initVariable();

        $params = $this->getRequest()->getParams();
        if(!isset($params['dynamic_grid']) || !count($params['dynamic_grid'])) {
            return $this->redirectForm(
                $params['return_id'],  __('Please select at least 1 product.')
            );
        }
        $productsData = $params['dynamic_grid'];
        foreach ($productsData as &$prdData) {
            $prdData['product_id'] = $prdData['id'];
            $prdData['return_id'] = $params['return_id'];
            $prdData['qty_returned'] = $prdData['returned_qty'];
            unset($prdData['id']);
            unset($prdData['returned_qty']);
            unset($prdData['record_id']);
        }

        $updateData = $this->getUpdatedData($productsData);

        try {
            $this->updateReturnProduct($updateData);
            $this->returnItemRepository->addProductsToReturnOrder($productsData);
            $this->returnService->updateQtyReturnOrder($this->returnOrderRepository->get($params['return_id']));
            return $this->redirectForm(
                $params['return_id'], __('Products have been added successfully.')
            );
        } catch (\Exception $e) {
            return $this->redirectForm(
                $params['return_id'], __('Cannot add product to return request.')
            );
        }
    }

    private function initVariable() {
        $this->returnProductFactory = $this->_objectManager->get('Magestore\PurchaseOrderSuccess\Model\ReturnOrder\ItemFactory');
    }

    public function getUpdatedData(&$productsData) {
        $updateData = [];
        $returnProductCollection = $this->returnProductFactory->create()
            ->getCollection()
            ->addFieldToFilter('return_id', $this->getRequest()->getParam('return_id'));

        $returnProductIds = [];
        foreach ($returnProductCollection as $prd) {
            $returnProductIds[$prd->getData('product_id')] = $prd->getData('return_item_id');
        }

        foreach ($productsData as $key => $data) {
            if(in_array($data['product_id'], array_keys($returnProductIds))) {
                $updateData[$returnProductIds[$data['product_id']]] = $data;
                unset($productsData[$key]);
            }
        }

        return $updateData;
    }

    public function updateReturnProduct($updateData) {
        foreach ($updateData as $id => $data) {
            $this->returnProductFactory->create()->load($id)
                ->addData($data)->save();
        }
    }
}