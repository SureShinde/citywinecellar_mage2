<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Controller\Adminhtml\Unconverted;

/**
 * Class \Magestore\Webpos\Controller\Adminhtml\Unconverted\Index
 */
class Index extends \Magestore\Webpos\Controller\Adminhtml\Unconverted\AbstractAction
{
    /**
     * Display the grid
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::unconvertedOrder');
        $resultPage->addBreadcrumb(__('Unconverted Order'), __('Unconverted Order'));
        $resultPage->getConfig()->getTitle()->prepend(__('Unconverted Order'));
        return $resultPage;
    }
}
