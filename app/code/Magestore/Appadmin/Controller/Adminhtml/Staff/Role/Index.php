<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Controller\Adminhtml\Staff\Role;
/**
 * class \Magestore\Appadmin\Controller\Adminhtml\Staff\Role\Index
 *
 * List staff
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Appadmin\Controller\Adminhtml\Staff\Role
 * @module      Appadmin
 * @author      Magestore Developer
 */
class Index extends \Magestore\Appadmin\Controller\Adminhtml\Staff\Role
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::pos');
        $resultPage->addBreadcrumb(__('Role'), __('Role'));
        $resultPage->getConfig()->getTitle()->prepend(__('Role'));
        return $resultPage;
    }
}