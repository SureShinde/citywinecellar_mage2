<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilReport\Controller\Adminhtml\Report\FulfilStaff;

class Index extends \Magestore\FulfilReport\Controller\Adminhtml\Report\AbstractReport
{
    /**
     * Sales report action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_setActiveMenu(
            'Magestore_FulfilReport::fulfil_reports'
        )->_addBreadcrumb(
            __('Fulfilment by staff'),
            __('Fulfilment by staff')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Fulfilment by staff'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_report_fulfilstaff.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form')->setFulfilAction(true);

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }

}