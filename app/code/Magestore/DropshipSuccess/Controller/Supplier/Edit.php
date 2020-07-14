<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller\Supplier;

/**
 * Class Edit
 * @package Magestore\DropshipSuccess\Controller\Supplier
 */
class Edit extends \Magestore\DropshipSuccess\Controller\AbstractSupplier
{
    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->checkLogin();
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Supplier Information'));
        return $resultPage;
    }
}
