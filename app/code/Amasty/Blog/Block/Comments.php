<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block;

use Amasty\Blog\Helper\Data;
use Amasty\Blog\Helper\Settings;
use Amasty\Blog\Block\Comments\Message;

class Comments extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var Settings
     */
    private $settingsHelper;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var \Amasty\Blog\Helper\Date
     */
    private $dateHelper;

    /**
     * @var \Amasty\Blog\Api\CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Blog\Helper\Settings $settingsHelper,
        \Amasty\Blog\Helper\Data $dataHelper,
        \Amasty\Blog\Helper\Date $dateHelper,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magento\Framework\Registry $registry,
        \Amasty\Blog\Api\CommentRepositoryInterface $commentRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->settingsHelper = $settingsHelper;
        $this->dataHelper = $dataHelper;
        $this->dateHelper = $dateHelper;
        $this->commentRepository = $commentRepository;
        $this->sessionFactory = $sessionFactory;
    }

    /**
     * @return \Amasty\Blog\Model\Posts|bool|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPost()
    {
        $result = false;
        $parent = $this->getParentBlock();
        if ($parent) {
            if ($parent instanceof \Amasty\Blog\Block\Content\Post) {
                $result = $parent->getPost();
            }
        } else {
            $result = $this->registry->registry('current_post');
        }

        if (!$result) {
            $result = $this->getData('post');
        }

        return $result;
    }

    /**
     * @return \Amasty\Blog\Model\ResourceModel\Comments\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCollection()
    {
        $comments = $this->commentRepository->getCommentsInPost($this->getPostId());

        $comments->addActiveFilter(
            $this->settingsHelper->getCommentsAutoapprove()
                ? null
                : $this->getSession()->getSessionId()
        );

        $comments->setDateOrder(\Magento\Framework\DB\Select::SQL_ASC)->setNotReplies();

        return $comments;
    }

    /**
     * @param \Amasty\Blog\Model\Comments $message
     *
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMessageHtml(\Amasty\Blog\Model\Comments $message)
    {
        $result = false;
        $messageBlock = $this->getLayout()->getBlock(Message::AMBLOG_COMMENTS_MESSAGE);
        if (!$messageBlock) {
            $messageBlock = $this->getLayout()
                ->createBlock(\Amasty\Blog\Block\Comments\Message::class, Message::AMBLOG_COMMENTS_MESSAGE)
                ->setTemplate("Amasty_Blog::comments/list/message.phtml");
        }
        if ($messageBlock) {
            $messageBlock->setMessage($message);
            $result = $messageBlock->toHtml();
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getFormUrl()
    {
        return $this->getUrl(
            'amblog/index/form',
            [
                'reply_to' => '{{reply_to}}',
                'post_id' => '{{post_id}}',
                'session_id' => '{{session_id}}',
            ]
        );
    }

    /**
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('amblog/index/updateComments');
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPostId()
    {
        return (int)$this->getPost()->getId();
    }

    /**
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl(
            'amblog/index/postForm',
            [
                'reply_to' => '{{reply_to}}',
                'post_id' => '{{post_id}}',
            ]
        );
    }

    /**
     * @return string
     */
    public function getColorClass()
    {
        return $this->getSettingsHelper()->getIconColorClass();
    }

    /**
     * @return bool
     */
    public function commentsEnabled()
    {
        return $this->settingsHelper->getUseComments();
    }

    /**
     * @return \Amasty\Blog\Helper\Date
     */
    public function getDateHelper()
    {
        return $this->dateHelper;
    }

    /**
     * @return Data
     */
    public function getDataHelper()
    {
        return $this->dataHelper;
    }

    /**
     * @return Settings
     */
    public function getSettingsHelper()
    {
        return $this->settingsHelper;
    }

    /**
     * @return \Magento\Customer\Model\Session|\Magento\Customer\Model\Session\Proxy
     */
    public function getSession()
    {
        return $this->sessionFactory->create();
    }

    /**
     * @return \Magento\Framework\Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @return \Amasty\Blog\Api\CommentRepositoryInterface
     */
    public function getRepository()
    {
        return $this->commentRepository;
    }
}
