<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Cron;

use Amasty\Blog\Api\Data\PostInterface;
use Amasty\Blog\Model\Source\PostStatus;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class Scheduled
 */
class Scheduled
{
    /**
     * @var \Amasty\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var DateTime\TimezoneInterface
     */
    private $date;

    public function __construct(
        \Amasty\Blog\Api\PostRepositoryInterface $postRepository,
        DateTime\TimezoneInterface $date
    ) {
        $this->postRepository = $postRepository;
        $this->date = $date;
    }

    /**
     * @param \Magento\Cron\Model\Schedule $schedule
     * @throws \Zend_Date_Exception
     */
    public function execute(\Magento\Cron\Model\Schedule $schedule)
    {
        $posts = $this->postRepository->getPostCollection();
        $posts->addFieldToFilter(PostInterface::STATUS, PostStatus::STATUS_SCHEDULED)
            ->addFieldToFilter(PostInterface::PUBLISHED_AT, ['lt' => new \Zend_Db_Expr('NOW()')]);

        foreach ($posts as $post) {
            $post->setStatus(PostStatus::STATUS_ENABLED);
            $this->postRepository->save($post);
        }
    }
}
