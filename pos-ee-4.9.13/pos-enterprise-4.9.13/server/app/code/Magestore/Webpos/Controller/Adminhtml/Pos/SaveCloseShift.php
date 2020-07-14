<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;


class SaveCloseShift extends \Magestore\Webpos\Controller\Adminhtml\Pos\AbstractAction
{
    /**
     * @return $this
     */
    public function execute()
    {
        $result = array();
        $request = $this->getRequest();
        $contentData = $request->getContent();
        $contentData = \Zend_Json::decode($contentData);
        $shift = $this->shipInterface->setData($contentData);
        $shiftIncrementId = $shift->getShiftIncrementId();
        $staffId = $shift->getStaffId();
        $posId = $shift->getPosId();

        $shiftModel = $this->_shiftFactory->create();

        if (!$shiftIncrementId) {
            throw new \StateException(__('Shift increment id is required'));
        }
        if (!$staffId) {
            throw new \StateException(__('Staff id is required'));
        }
        if (!$posId) {
            throw new \StateException(__('Pos id is required'));
        }
        $shiftModel->load($shiftIncrementId, "shift_increment_id");
        if ($shiftModel->getShiftIncrementId()) {
            $shift->setId($shiftModel->getId());
        } else {
            $shift->setId(null);
            if ($this->posIsOpened($posId)) {
                throw new \StateException(__('Please close your session before opening a new one'));
            }
        }
        try {
            $this->shiftResource->save($shift);
            $result['success'] = true;
        } catch (\Exception $exception) {
            throw new \CouldNotSaveException(__($exception->getMessage()));
        }

        $resultJson = $this->jsonFactory->create();
        return $resultJson->setData($result);
    }

    /**
     * @param $posId
     * @return bool
     */
    public function posIsOpened($posId)
    {
        $collection = $this->_shiftCollectionFactory->create();
        $collection
            ->addFieldToFilter(\Magestore\Webpos\Api\Data\Shift\ShiftInterface::POS_ID, $posId)
            ->addFieldToFilter(\Magestore\Webpos\Api\Data\Shift\ShiftInterface::STATUS,
                                \Magestore\Webpos\Api\Data\Shift\ShiftInterface::OPEN_STATUS);
        return $collection->getSize() > 0;
    }


    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::pos');
    }
}