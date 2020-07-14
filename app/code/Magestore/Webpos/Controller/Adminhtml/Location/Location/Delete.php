<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Location\Location;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Location\Location\Delete
 *
 * Delete user
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Location\Location
 * @module      Webpos
 * @author      Magestore Developer
 */
/**
 * Class Delete
 * @package Magestore\Webpos\Controller\Adminhtml\Location\Location
 */
class Delete extends \Magestore\Webpos\Controller\Adminhtml\Location\Location
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        try {
            $this->locationRepository->deleteById($id);
            $this->messageManager->addSuccessMessage('Items deleted successfully!');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->redirectResult('*/*/index');
    }
}