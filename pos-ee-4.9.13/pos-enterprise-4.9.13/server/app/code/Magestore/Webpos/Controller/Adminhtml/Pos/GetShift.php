<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;


class GetShift extends \Magestore\Webpos\Controller\Adminhtml\Pos\AbstractAction
{
    /**
     * @return $this
     */
    public function execute()
    {
        $shift = $this->getCurrentShift();
        if (!$shift || !$shift->getId()) {
            $result = [
                'session_data' => ["error" => __('There is no session in progress.')]
            ];
        } else {
            /** @var \Magestore\Webpos\Helper\Shift $shiftHelper */
            $shiftHelper = $this->_objectManager->create('Magestore\Webpos\Helper\Shift');
            $result = [
                'session_data' => $shiftHelper->getShiftDataForAdmin($shift)
            ];
            $posId = $this->getRequest()->getParam('pos_id');
            if ($posId) {
                $result['currency_data'] = $this->getCurrencyData($shift);
            }
        }
        $result["session_data"]["refresh_url"] = $this->getRefreshUrl();
        $result["session_data"]["save_url"] = $this->getSaveUrl();
        $result["session_data"]["save_close_shift_url"] = $this->getSaveCloseShiftUrl();

        $resultJson = $this->jsonFactory->create();
        return $resultJson->setData($result);
    }

    /**
     * Get Cur
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftInterface|null
     */
    public function getCurrentShift()
    {
        $shiftId = $this->getRequest()->getParam('id');
        /** @var \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface $shiftRepository */
        $shiftRepository = $this->_objectManager->create('Magestore\Webpos\Api\Shift\ShiftRepositoryInterface');
        if ($shiftId && $shiftId > 0) {
            return $shiftRepository->get($shiftId);
        }
        $posId = $this->getRequest()->getParam('pos_id');
        if ($posId && $posId > 0) {
            return $shiftRepository->getCurrentShiftByPosId($posId);
        }
        return null;
    }

    /**
     * Get currency data
     *
     * @return array
     */
    public function getCurrencyData($shift)
    {
        $output = [];
        /** @var \Magento\Framework\Locale\CurrencyInterface $localeCurrency */
        $localeCurrency = $this->_objectManager->get('Magento\Framework\Locale\CurrencyInterface');
        $currentCurrency = $localeCurrency->getCurrency($shift->getShiftCurrencyCode());
        $baseCurrency = $localeCurrency->getCurrency($shift->getBaseCurrencyCode());
        /** @var \Magento\Framework\Locale\FormatInterface $localeFormat */
        $localeFormat = $this->_objectManager->get('Magento\Framework\Locale\FormatInterface');
        $output['currentCurrencyCode'] = $shift->getShiftCurrencyCode();
        $output['baseCurrencyCode'] = $shift->getBaseCurrencyCode();
        $output['currentCurrencySymbol'] = $currentCurrency->getSymbol();
        $output['baseCurrencySymbol'] = $baseCurrency->getSymbol();
        $output['priceFormat'] = $localeFormat->getPriceFormat(null, $shift->getShiftCurrencyCode());
        $output['basePriceFormat'] = $localeFormat->getPriceFormat(null, $shift->getBaseCurrencyCode());
        return $output;
    }

    /**
     * @return string
     */
    public function getRefreshUrl()
    {
        $shiftId = $this->getRequest()->getParam('id');
        if ($shiftId) {
            return $this->_url->getUrl("*/*/getShift", ['id' => $shiftId]);
        }
        $posId = $this->getRequest()->getParam('pos_id');
        return $this->_url->getUrl("*/*/getShift", ['pos_id' => $posId]);
    }

    /**
     * @return string
     */
    public function getSaveUrl(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $FormKey = $objectManager->get('Magento\Framework\Data\Form\FormKey');
        return $this->_url->getUrl("*/*/saveShift", ['form_key' => $FormKey->getFormKey()]);
    }

    /**
     * @return string
     */
    public function getSaveCloseShiftUrl(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $FormKey = $objectManager->get('Magento\Framework\Data\Form\FormKey');
        return $this->_url->getUrl("*/*/saveCloseShift", ['form_key' => $FormKey->getFormKey()]);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::pos');
    }
}