<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;


class SaveShift extends \Magestore\Webpos\Controller\Adminhtml\Pos\AbstractAction
{
    /**
     * @return $this
     */
    public function execute()
    {
        $request = $this->getRequest();
        $contentData = $request->getContent();
        $contentData = \Zend_Json::decode($contentData);
        $cashTransaction = $this->cashTransactionInterface->setData($contentData);
        $shiftModel = null;
        $shiftIncrementId = $cashTransaction->getShiftIncrementId();
        if (!$shiftModel || !$shiftModel->getShiftIncrementId() ||
            $shiftModel->getShiftIncrementId() != $shiftIncrementId) {
            $shiftModel = $this->_shiftFactory->create()->load($shiftIncrementId, "shift_increment_id");
            if (!$shiftModel->getShiftIncrementId()) {
                throw new \StateException(__('Shift increment id is required'));
            }
        }
        try {
            $cashTransaction->setShiftIncrementId($shiftIncrementId);
            $this->cashTransactionResource->save($cashTransaction);
            $cashCollection = $this->_objectManager->get('Magestore\Webpos\Model\Shift\CashTransaction')
                ->getCollectionByShift($shiftIncrementId);
        } catch (\Exception $exception) {
            throw new \CouldNotSaveException(__($exception->getMessage()));
        }
        if ($shiftModel) {
            $shiftModel->setUpdatedAt(date('Y-m-d H:i:s'))->save();
        }
        $resultJson = $this->jsonFactory->create();
        return $resultJson->setData($cashCollection);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::pos');
    }
}