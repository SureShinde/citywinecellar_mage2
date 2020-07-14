<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Controller\Adminhtml\Unconverted;

/**
 * Class \Magestore\Webpos\Controller\Adminhtml\Unconverted\View
 */
class View extends \Magestore\Webpos\Controller\Adminhtml\Unconverted\AbstractAction
{
    /**
     * View order
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $posOrder = $this->_objectManager->get(\Magestore\Webpos\Model\Checkout\PosOrder::class)
            ->load($this->getRequest()->getParam('id'));
        if ($posOrder->getId()) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Magestore_Webpos::unconvertedOrder');
            $resultPage->addBreadcrumb(__('Unconverted Order'), __('Unconverted Order'));
            $resultPage->getConfig()->getTitle()->prepend(__('Unconverted Order #%1', $posOrder->getIncrementId()));
            return $resultPage;
        }
    }
}
