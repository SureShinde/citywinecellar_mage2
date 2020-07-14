<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Completed;

/**
 * Class Index
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Completed
 */
class Index extends \Magestore\OrderSuccess\Controller\Adminhtml\OrderAbstract
{
    
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_OrderSuccess::completed';
    
    /**
     * Orders grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Completed orders'));
        return $resultPage;
    }
}
