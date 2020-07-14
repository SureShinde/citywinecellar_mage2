<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Post;

class Preview extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Amasty\Blog\Model\PostsFactory
     */
    private $postsFactory;

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
     * @var \Amasty\Blog\Model\Preview\PreviewSession
     */
    private $previewSession;

    /**
     * @var \Magento\Framework\App\Cache\Type\Block
     */
    private $cache;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Blog\Model\PostsFactory $postsFactory,
        \Magento\Store\App\Response\Redirect $redirect,
        \Amasty\Blog\Helper\Url $urlHelper,
        \Amasty\Blog\Model\Preview\PreviewSession $previewSession,
        \Magento\Framework\App\Cache\Type\Block $cache,
        \Amasty\Base\Model\Serializer $serializer
    ) {
        parent::__construct($context);
        $this->postsFactory = $postsFactory;
        $this->registry = $registry;
        $this->urlHelper = $urlHelper;
        $this->redirect = $redirect;
        $this->previewSession = $previewSession;
        $this->cache = $cache;
        $this->serializer = $serializer;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $data = $this->getSavedData();
        if ($data) {
            if (strpos($this->getRequest()->getPathInfo(), '/amp/') !== false) {
                $this->urlHelper->addAmpHeaders($this->getResponse());
            }

            $post = $this->postsFactory->create();
            $post->addData($data);
            $post->setIsPreviewPost(true);
            $post->setCommentsEnabled(false);

            $this->registry->unregister('current_post');
            $this->registry->register('current_post', $post);
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } else {
            $this->_redirect('noroute');
        }
    }

    /**
     * @return array
     */
    protected function getSavedData()
    {
        $data = [];
        $blogKey = $this->getRequest()->getParam('amblog_key');
        if ($blogKey) {
            $data = $this->cache->load($blogKey);
            if ($data) {
                $this->cache->remove($blogKey);
                $data = $this->serializer->unserialize($data);
                $this->previewSession->setPostData($data);
            }
        }

        if (!$data) {
            $data = $this->previewSession->getPostData();
        }

        return $data ?: [];
    }
}
