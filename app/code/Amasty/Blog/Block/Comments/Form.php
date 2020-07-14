<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Comments;

use Amasty\Blog\Api\Data\PostInterface;

/**
 * Class
 */
class Form extends \Amasty\Blog\Block\Comments
{
    const KEY_CUSTOMER_NAME = 'amblog-customer-name';

    const KEY_CUSTOMER_EMAIL = 'amblog-customer-email';

    /**
     * @var string
     */
    protected $_template = 'Amasty_Blog::comments/form.phtml';

    /**
     * @var string|null
     */
    private $post;

    /**
     * @var string|null
     */
    private $replyTo;

    /**
     * @var \Amasty\Blog\Model\ConfigProvider
     */
    private $configProvider;

    public function __construct(
        \Amasty\Blog\Model\ConfigProvider $configProvider,
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Blog\Helper\Settings $settingsHelper,
        \Amasty\Blog\Helper\Data $dataHelper,
        \Amasty\Blog\Helper\Date $dateHelper,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magento\Framework\Registry $registry,
        \Amasty\Blog\Api\CommentRepositoryInterface $commentRepository,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $settingsHelper,
            $dataHelper,
            $dateHelper,
            $sessionFactory,
            $registry,
            $commentRepository,
            $data
        );
        $this->configProvider = $configProvider;
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if ($this->getPost()) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * @param $value
     * @return $this
     */
    public function setPost($value)
    {
        $this->post = $value;

        return $this;
    }

    /**
     * @param $value
     */
    public function setReplyTo($value)
    {
        $this->replyTo = $value;
    }

    /**
     * @return int
     */
    public function getReplyTo()
    {
        return $this->replyTo ? $this->replyTo->getId() : 0;
    }

    /**
     * @return PostInterface
     */
    public function getPost()
    {
        if (!$this->post) {
            $this->post = $this->getRegistry()->registry('current_post');
        }

        return $this->post;
    }

    /**
     * @return int
     */
    public function getPostId()
    {
        return (int)$this->getPost()->getId();
    }

    /**
     * @return bool
     */
    public function isReply()
    {
        return !!$this->getReplyTo();
    }

    /**
     * @return bool
     */
    public function canPost()
    {
        return $this->getPost()->getCommentsEnabled();
    }

    /**
     * @return bool
     */
    public function canUserPost()
    {
        return $this->getSettingsHelper()->getCommentsAllowGuests() || $this->isLoggedIn();
    }

    /**
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->getUrl('customer/account/create');
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        $params = ['post_id' => $this->getPostId()];
        if ($this->isReply()) {
            $params['reply_to'] = $this->getReplyTo();
        }

        return $this->getUrl('customer/account/login', $params);
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->getSession()->isLoggedIn();
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->getSession()->getCustomerId();
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->isLoggedIn()
            ? $this->getSession()->getCustomer()->getName()
            : $this->getSession()->getData(self::KEY_CUSTOMER_NAME);
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->isLoggedIn()
            ? $this->getSession()->getCustomer()->getEmail()
            : $this->getSession()->getData(self::KEY_CUSTOMER_EMAIL);
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->getData('session_id');
    }

    /**
     * @return bool|string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getReplyToCustomerName()
    {
        $comment = $this->getRepository()->getById($this->getReplyTo());

        return $comment->getId() ? $comment->getName() : false;
    }

    /**
     * @return bool
     */
    public function isGdprEnabled()
    {
        return (bool)$this->configProvider->isShowGdpr();
    }

    /**
     * @return string
     */
    public function getGdprText()
    {
        return $this->configProvider->getGdprText();
    }

    /**
     * @return bool
     */
    public function isAskEmail()
    {
        return $this->configProvider->isAskEmail();
    }

    /**
     * @return bool
     */
    public function isAskName()
    {
        return $this->configProvider->isAskName();
    }

    /**
     * @return string
     */
    public function getAmpFormUrl()
    {
        $url = $this->getUrl(
            'amblog/index/postForm',
            [
                'post_id' => $this->getPostId(),
                'session_id' => $this->getSessionId(),
                'reply_to' => $this->getReplyTo()
            ]
        );

        return str_replace(['https:', 'http:'], '', $url);
    }

    /**
     * @return bool
     */
    public function isCommentAdded()
    {
        return (bool)$this->getRequest()->getParam('amcomment');
    }
}
