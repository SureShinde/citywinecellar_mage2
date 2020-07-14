<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Appadmin\Controller\Adminhtml\Staff\Staff;
/**
 * class \Magestore\Appadmin\Controller\Adminhtml\Staff\Staff\Index
 *
 * List staff
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Appadmin\Controller\Adminhtml\Staff
 * @module      Appadmin
 * @author      Magestore Developer
 */
class Index extends \Magestore\Appadmin\Controller\Adminhtml\Staff\Staff
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::pos');
        $resultPage->addBreadcrumb(__('Staff'), __('Staff'));
        $resultPage->getConfig()->getTitle()->prepend(__('Staff'));
        return $resultPage;
    }
}