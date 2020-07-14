<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Posts;

class Edit extends \Amasty\Blog\Controller\Adminhtml\Posts
{
    const CURRENT_AMASTY_BLOG_POST = 'current_amasty_blog_post';

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->getPostRepository()->getPost();
        if ($id) {
            try {
                $model = $this->getPostRepository()->getById($id);
            } catch (\Exception $e) {
                $this->getMessageManager()->addErrorMessage($e->getMessage());
                $this->_redirect('*/*');

                return;
            }
        }

        // set entered data if was error when we do save
        $data = $this->_getSession()->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->getRegistry()->register(self::CURRENT_AMASTY_BLOG_POST, $model);
        $this->initAction();

        $title = $model->getId() ? __('Edit Post `%1`', $model->getTitle()) : __("Add New Post");

        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);
        $this->_view->renderLayout();
    }

    /**
     * @return $this
     */
    private function initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Amasty_Blog::posts')->_addBreadcrumb(
            __('Amasty Blog Posts'),
            __('Amasty Blog Posts')
        );

        return $this;
    }
}
