<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Tags;

/**
 * Class Index
 */
class Index extends \Amasty\Blog\Controller\Adminhtml\Tags
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->getPageFactory()->create();
        $resultPage->setActiveMenu('Amasty_Blog::tags');
        $resultPage->getConfig()->getTitle()->prepend(__('Tags'));
        $resultPage->addBreadcrumb(__('Tags'), __('Tags'));

        return $resultPage;
    }
}
