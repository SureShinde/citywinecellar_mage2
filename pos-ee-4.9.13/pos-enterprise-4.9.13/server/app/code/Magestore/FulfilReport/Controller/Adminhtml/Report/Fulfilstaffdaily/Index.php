<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilReport\Controller\Adminhtml\Report\Fulfilstaffdaily;

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
            __('Fulfilment by staff (Daily)'),
            __('Fulfilment by staff (Daily)')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Fulfilment by staff (Daily)'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_report_fulfilstaffdaily.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form')->setFulfilAction(true);

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }

}