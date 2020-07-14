<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Controller\Adminhtml\Location\Location;

class Edit extends \Magestore\Webpos\Controller\Adminhtml\Location\Location {
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model = $this->locationRepository->getById($id);
        } else {
            $model = $this->locationInterfaceFactory->create();
        }
        $this->registry->register('current_location', $model);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::locations');
        if (!$model->getLocationId()) {
            $pageTitle = __('Add New Location');
        } else {
            $pageTitle =  __('Edit %1', $model->getName());
        }

        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }
}