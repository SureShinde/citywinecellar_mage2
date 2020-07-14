<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Tags;

use Magento\Backend\App\Action;

/**
 * Class
 */
class Tagged extends Action
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    private $resultLayoutFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Amasty\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Registry $registry,
        \Amasty\Blog\Api\PostRepositoryInterface $postRepository
    ) {
        parent::__construct($context);

        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->coreRegistry = $registry;
        $this->postRepository = $postRepository;
    }

    public function execute()
    {
        $tagId = (int)$this->_request->getParam('id');
        if ($tagId) {
            $postsCollection = $this->postRepository->getTaggedPosts($tagId);
            $this->coreRegistry->register('amasty_blog_current_posts', $postsCollection);
        }

        return $this->resultLayoutFactory->create();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Blog::tags');
    }
}
