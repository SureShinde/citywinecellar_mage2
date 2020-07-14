<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\AdjustStock\Controller\Adminhtml\AdjustStock;

/**
 * Class NewAction
 * @package Magestore\AdjustStock\Controller\Adminhtml\AdjustStock
 */
class NewAction extends \Magestore\AdjustStock\Controller\Adminhtml\AdjustStock\AdjustStock
{
    /**
     * Create new customer action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();
        $resultForward->forward('edit');
        return $resultForward;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_AdjustStock::create_adjuststock');
    }

}
