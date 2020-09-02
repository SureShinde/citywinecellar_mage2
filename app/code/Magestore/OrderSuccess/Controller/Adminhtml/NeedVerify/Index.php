<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\NeedVerify;

/**
 * Class Index
 * @package Magestore\OrderSuccess\Controller\Adminhtml\NeedVerify
 */
class Index extends \Magestore\OrderSuccess\Controller\Adminhtml\OrderAbstract
{
    
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_OrderSuccess::need_to_verify';
    
    /**
     * Orders grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $pageTitle = new \Magento\Framework\DataObject(['title' => __('Sales Success - Verify Orders')]);
        $this->_eventManager->dispatch(
                'ordersuccess_verify_order_page_title',
                ['page_title' => $pageTitle]
        );
        $resultPage->getConfig()->getTitle()->prepend($pageTitle->getTitle());
        return $resultPage;
    }
}
