<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Controller\Adminhtml\Pos;

/**
 * Class Index
 * @package Magestore\Webpos\Controller\Adminhtml\Pos
 */
class Index extends \Magestore\Webpos\Controller\Adminhtml\Pos\AbstractAction
{

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::pos');
    }

    /**
     * Warehouse grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('POS'));
        return $resultPage;
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::managepos');
        $resultPage->getConfig()->getTitle()->prepend(__('POS'));
        $resultPage->addBreadcrumb(__('Manage Pos'), __('POS'));
        return $resultPage;
    }
}
