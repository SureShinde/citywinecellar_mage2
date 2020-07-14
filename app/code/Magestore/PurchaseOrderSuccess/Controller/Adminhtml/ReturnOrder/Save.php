<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder;

/**
 * Class Save
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Quotation
 */
class Save extends AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::save_return_order';

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|Save
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if ($this->localeDate->date()->format('Y-m-d') != $params['returned_at']) {
            $filterValues = ['returned_at' => $this->dateFilter];
            $inputFilter = new \Zend_Filter_Input(
                $filterValues,
                [],
                $params
            );
            $params = $inputFilter->getUnescaped();
        }
        $id = (isset($params['return_id']) && $params['return_id']>0)?$params['return_id']:null;
        if($id){
            try{
                $returnOrder = $this->returnOrderRepository->get($id);
            }catch (\Exception $e){
                return $this->redirectGrid($e->getMessage());
            }
        }else{
            $returnOrder = $this->_returnOrderFactory->create();
        }
        $returnOrder->addData($params)->setId($id);
        $canSendEmail = $returnOrder->canSendEmail();
        try{
            $returnOrder = $this->returnOrderRepository->save($returnOrder);
            $productsData = $this->itemService->processUpdateProductParams($params);
            if(!empty($productsData)){
                $this->itemService->updateProductDataToReturnOrder($returnOrder, $productsData);
                $this->returnService->updateQtyReturnOrder($returnOrder);
            }
        }catch (\Exception $e){
            return $this->redirectForm(
                $id,
                $e->getMessage(),
                \Magento\Framework\Message\MessageInterface::TYPE_ERROR
            );
        }
        if($this->getRequest()->getParam('isConfirm') == 'true'){
            $resultForward = $this->_resultForwardFactory->create();;
            $resultForward->setParams($this->getRequest()->getParams());
            return $resultForward->forward('confirm');
        }
        if($canSendEmail && isset($params['sendEmail']) && $params['sendEmail'] == 'true'){
            $supplier = $this->supplierRepository->getById($params['supplier_id']);

            $warehouse = $this->sourceRepository->get($params['warehouse_id']);
            $this->_registry->register('current_return_order_warehouse', $warehouse);

            $this->_registry->register('current_return_order', $returnOrder);
            $this->_registry->register('current_return_order_supplier', $supplier);
            $sendSuccess = $this->returnService->sendEmailToSupplier($returnOrder, $supplier);
            if ($sendSuccess)
                return $this->redirectForm($id, __('An email has been sent to supplier'));
            else
                return $this->redirectForm(
                    $id,
                    __('Could not send email to supplier'),
                    \Magento\Framework\Message\MessageInterface::TYPE_ERROR
                );
        }
        return $this->redirectForm($returnOrder->getReturnOrderId(), __('Return request has been saved.'));
    }

}