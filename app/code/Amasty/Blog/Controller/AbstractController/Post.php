<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\AbstractController;

class Post extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Amasty\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Amasty\Blog\Helper\Url
     */
    private $urlHelper;

    /**
     * @var \Magento\Store\App\Response\Redirect
     */
    private $redirect;

    /**
     * @var \Amasty\Blog\Model\UrlResolver
     */
    private $urlResolver;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Blog\Api\PostRepositoryInterface $postRepository,
        \Magento\Store\App\Response\Redirect $redirect,
        \Amasty\Blog\Helper\Url $urlHelper,
        \Amasty\Blog\Model\UrlResolver $urlResolver
    ) {
        parent::__construct($context);
        $this->postRepository = $postRepository;
        $this->registry = $registry;
        $this->urlHelper = $urlHelper;
        $this->redirect = $redirect;
        $this->urlResolver = $urlResolver;
    }

    public function execute()
    {
        if (strpos($this->getRequest()->getPathInfo(), '/amp/') !== false) {
            $this->urlHelper->addAmpHeaders($this->getResponse());
        }

        $postId = (int)$this->getRequest()->getParam("id");
        if ($postId) {
            try {
                $post = $this->postRepository->getById($postId);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('*/*');
                return;
            }
            if ($post->getId()) {
                $this->registry->unregister('current_post');
                $this->registry->register('current_post', $post);
                $this->_view->loadLayout();
                $this->_view->renderLayout();
            } else {
                $this->getResponse()->setRedirect($this->urlResolver->getBlogUrl());
            }
        } else {
            $this->getResponse()->setRedirect($this->urlResolver->getBlogUrl());
        }
    }
}
