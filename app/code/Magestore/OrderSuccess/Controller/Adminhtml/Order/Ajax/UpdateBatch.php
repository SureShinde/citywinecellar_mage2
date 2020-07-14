<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order\Ajax;

/**
 * Class UpdateBatch
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Order\Ajax
 */
class UpdateBatch extends \Magestore\OrderSuccess\Controller\Adminhtml\Order\OrderAction
{

    /**
     * update order batch
     *
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = '';
        $updateBatch = false;
        $orderId = $this->_request->getParam('order_id');
        $batchId = $this->_request->getParam('batch');
        if($orderId) {
            try {
                if ($batchId == 'remove') {
                    $this->batchService->removeOrderFromBatch($orderId);
                } else if ($batchId == 'newbatch') {
                    $batch = $this->batchRepository->newBatch();
                    $updateBatch = true;
                    $batch = $this->batchService->addOrderToBatch($batch, $orderId);
                }else {
                    $batch = $this->batchRepository->getById($batchId);
                    $this->batchService->addOrderToBatch($batch, $orderId);
                }
            }catch(\Exception $e){
                $error = true;
                $messages = __('Can not update batch for the order');
            }
        } else {
            $error = true;
        }
        $data = [
            'messages' => $messages,
            'error' => $error
        ];
        if($updateBatch){
            $data['html'] = $this->getBatchHtml($batch);
        }
        return $resultJson->setData($data);
    }

    /**
     * Get Batch Html
     *
     * @param $tagColors
     */
    public function getBatchHtml($batch)
    {
        $html = '';
        if($batch->getId()){
            $html .= '<option selected value="'.$batch->getId().'">
                            '.$batch->getCode().'
                    </option>';
        }
        return $html;
    }
}