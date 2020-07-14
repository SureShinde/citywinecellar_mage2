<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Hold;

/**
 * Class Index
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Hold
 */
class Index extends \Magestore\OrderSuccess\Controller\Adminhtml\OrderAbstract
{
    
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_OrderSuccess::hold';
    
    /**
     * Orders grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Hold orders'));
        return $resultPage;
    }
}
