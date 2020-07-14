<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Index;

use Magento\Framework\App\Action;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class
 */
class UpdateComments extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Amasty\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var Validator
     */
    private $formKeyValidator;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Amasty\Blog\Api\PostRepositoryInterface $postRepository,
        Validator $formKeyValidator
    ) {
        parent::__construct($context);
        $this->postRepository = $postRepository;
        $this->formKeyValidator = $formKeyValidator;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $result = [];
        $postId = (int)$this->getRequest()->getParam('post_id');
        try {
            if (!$this->formKeyValidator->validate($this->getRequest())) {
                throw new LocalizedException(
                    __('We can\'t load comments right now. Please reload the page.')
                );
            }

            $post = $this->postRepository->getById($postId);

            /** @var \Amasty\Blog\Block\Comments $block */
            $block = $this->_view->getLayout()->getBlock('amblog.comments.list');
            if (!$block) {
                $block = $this->_view->getLayout()
                    ->createBlock(\Amasty\Blog\Block\Comments::class)
                    ->setTemplate("Amasty_Blog::comments/list.phtml");
            }

            if ($block) {
                $block->setPost($post);
                $result['content'] = $block->toHtml();
            }

            $shortComment = $this->_view->getLayout()
                ->createBlock(\Amasty\Blog\Block\Content\Post\Details::class)
                ->setTemplate("Amasty_Blog::post/short_comments.phtml");
            if ($shortComment) {
                $shortComment->setPost($post);
                $result['short_content'] = $shortComment->toHtml();
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(\Zend_Json::encode($result));
    }
}
