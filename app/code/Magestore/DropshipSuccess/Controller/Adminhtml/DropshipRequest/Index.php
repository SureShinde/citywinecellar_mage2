<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Controller\Adminhtml\DropshipRequest;

/**
 * Class Index
 * @package Magestore\DropshipSuccess\Controller\Adminhtml\DropshipRequest
 */
/**
 * Class Index
 * @package Magestore\DropshipSuccess\Controller\Adminhtml\DropshipRequest
 */
class Index extends \Magestore\DropshipSuccess\Controller\Adminhtml\AbstractDropship
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_DropshipSuccess::dropship_request_listing';

    /**
     * History action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_DropshipSuccess::dropship');
        $resultPage->getConfig()->getTitle()->prepend(__('Dropship'));
        $resultPage->addBreadcrumb(__('Dropship Success'), __('Dropship Success'));
        $resultPage->addBreadcrumb(__('Dropship Request'), __('Dropship Request'));
        return $resultPage;
    }
}
