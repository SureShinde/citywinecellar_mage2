<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Appadmin\Controller\Adminhtml\Staff\Role;

/**
 * class \Magestore\Appadmin\Controller\Adminhtml\Staff\Role\UserGrid
 *
 * Staff Grid
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Appadmin\Controller\Adminhtml\Staff\Role
 * @module      Appadmin
 * @author      Magestore Developer
 */
class StaffGrid extends \Magestore\Appadmin\Controller\Adminhtml\Staff\Role
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
