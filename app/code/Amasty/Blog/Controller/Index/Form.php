<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Index;

use Magento\Framework\App\Action;

/**
 * Class Form
 */
class Form extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Amasty\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var \Amasty\Blog\Api\CommentRepositoryInterface
     */
    private $commentRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Amasty\Blog\Api\PostRepositoryInterface $postRepository,
        \Amasty\Blog\Api\CommentRepositoryInterface $commentRepository
    ) {
        parent::__construct($context);
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $result = [];

        $postId = (int)$this->getRequest()->getParam('post_id');
        $sessionId = $this->getRequest()->getParam('session_id');
        try {
            if ($postId) {
                $post = $this->postRepository->getById($postId);
                $replyTo = (int)$this->getRequest()->getParam('reply_to');

                if ($replyTo) {
                    $comment = $this->commentRepository->getById($replyTo);
                }
                /** @var \Amasty\Blog\Block\Comments\Form $form */
                $form = $this->_view->getLayout()->createBlock(\Amasty\Blog\Block\Comments\Form::class);
                if ($form) {
                    $form->setPost($post)->setSessionId($sessionId);
                    if (isset($comment)) {
                        $form->setReplyTo($comment);
                    }
                    $result['form'] = $form->toHtml();
                }
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(\Zend_Json::encode($result));
    }
}
