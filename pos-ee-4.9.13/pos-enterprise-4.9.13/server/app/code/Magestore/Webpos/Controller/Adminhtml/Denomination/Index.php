<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Denomination;

/**
 * Class Index
 * @package Magestore\Webpos\Controller\Adminhtml\Denomination
 */
class Index extends \Magestore\Webpos\Controller\Adminhtml\Denomination\AbstractDenomination
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::denomination');
        $resultPage->addBreadcrumb(__('Denomination'), __('Denomination'));
        $resultPage->getConfig()->getTitle()->prepend(__('Denomination'));
        return $resultPage;
    }
}
