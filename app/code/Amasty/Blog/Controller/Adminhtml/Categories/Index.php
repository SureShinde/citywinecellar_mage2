<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Categories;

/**
 * Class Index
 */
class Index extends \Amasty\Blog\Controller\Adminhtml\Categories
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->getPageFactory()->create();
        $resultPage->setActiveMenu('Amasty_Blog::categories');
        $resultPage->getConfig()->getTitle()->prepend(__('Categories'));
        $resultPage->addBreadcrumb(__('Categories'), __('Categories'));

        return $resultPage;
    }
}
