<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\Comment;

use Amasty\Blog\Model\Source\CommentStatus;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Mail\Template\TransportBuilder;

/**
 * Class Notification emails for admin
 */
class Notification
{
    /**
     * @var \Amasty\Blog\Helper\Settings
     */
    private $settingsHelper;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var UrlInterface
     */
    private $backendUrl;

    public function __construct(
        \Amasty\Blog\Helper\Settings $settingsHelper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        TransportBuilder $transportBuilder
    ) {
        $this->settingsHelper = $settingsHelper;
        $this->transportBuilder = $transportBuilder;
        $this->backendUrl = $backendUrl;
    }

    /**
     * @param int $storeId
     * @param \Amasty\Blog\Api\Data\PostInterface $post
     * @param \Amasty\Blog\Model\Comments $comment
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function commentNotificationForAdmin($storeId, $comment, $post)
    {
        if (!$this->settingsHelper->getCommentNotificationsEnabled()) {
            return false;
        }

        $template = $this->settingsHelper->getNotificationEmailTemplate();
        $sender = $this->settingsHelper->getNotificationSender();
        $receivers = $this->settingsHelper->getNotificationRecievers();
        if (!$template || !$receivers) {
            return false;
        }

        $mainReceiver = array_shift($receivers);
        $transport = $this->transportBuilder->setTemplateIdentifier(
            $template
        )->setTemplateOptions(
            [
                'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ]
        )->setTemplateVars(
            [
                'post_title'    => $post->getTitle(),
                'post_link'    => $post->getUrl(),
                'comment_name' => $comment->getName(),
                'comment_email' => $comment->getEmail(),
                'comment_message' => $comment->getMessage(),
                'link' => $this->getCommentUrl($comment->getCommentId()),
                'need_approval' => $comment->getStatus() == CommentStatus::STATUS_PENDING
            ]
        )->setFrom(
            $sender
        )->addTo(
            $mainReceiver
        )->addTo(
            $receivers
        )->getTransport();

        $transport->sendMessage();
    }

    /**
     * @param int $commentId
     *
     * @return string
     */
    protected function getCommentUrl($commentId)
    {
        return $this->backendUrl->getUrl(
            'amasty_blog/comments/edit',
            [
                'id' => $commentId
            ]
        );
    }
}
