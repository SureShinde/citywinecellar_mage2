<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Controller\Adminhtml\Location\Location;

    /**
     * class \Magestore\Webpos\Controller\Adminhtml\Location\Location\Save
     *
     * Save user
     * Methods:
     *  execute
     *
     * @category    Magestore
     * @package     Magestore\Webpos\Controller\Adminhtml\Location\Location
     * @module      Webpos
     * @author      Magestore Developer
     */
/**
 * Class Save
 * @package Magestore\Webpos\Controller\Adminhtml\Staff\Staff
 */
class Save extends \Magestore\Webpos\Controller\Adminhtml\Location\Location
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('location_id');
        $data = $this->getRequest()->getPostValue();
        $arr1 = $data['general_information']['sub_general_information'];
        $arr2 = isset($data['general_information']['stock_selection']) ?
            $data['general_information']['stock_selection'] :
            [];
        $arr3 = $data['general_information']['address_information'];
        $arr4 = $data['custom_receipt']['container_receipt_header'];
        $arr5 = $data['custom_receipt']['container_receipt_footer'];
        $arr6 = $data['store_selection'];
        $arr = array_merge($arr1, $arr2, $arr3, $arr4, $arr5, $arr6);

        if (!$arr) {
            return $this->redirectResult('*/*/');
        }

        /** @var \Magestore\Webpos\Api\WebposManagementInterface $webposManagement */
        $webposManagement = $this->_objectManager->get('Magestore\Webpos\Api\WebposManagementInterface');
        if ($webposManagement->isMSIEnable() && $webposManagement->isWebposStandard()) {
            /** @var \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider */
            $defaultStockProvider = $this->_objectManager->create('Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface');
            $arr['stock_id'] = $defaultStockProvider->getId();
        }

        if ($id) {
            $model = $this->locationRepository->getById($id);
        } else {
            $model = $this->locationInterfaceFactory->create();
        }

        $model->setData($arr);

        try {
            $this->locationRepository->save($model);
            $this->messageManager->addSuccessMessage(__('You saved the location.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        if ($this->getRequest()->getParam('back') == 'edit') {
            return $this->redirectResult('*/*/edit', ['id' => $model->getLocationId()]);
        }

        return $this->redirectResult('*/*/');
    }
}