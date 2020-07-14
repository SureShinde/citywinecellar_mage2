<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Sidebar;

class Recentcomments extends AbstractClass
{
    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    private $collection;

    /**
     * @var \Amasty\Blog\Api\CommentRepositoryInterface
     */
    private $commentRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Blog\Helper\Settings $settingsHelper,
        \Amasty\Blog\Helper\Date $dateHelper,
        \Amasty\Blog\Helper\Data $dataHelper,
        \Amasty\Blog\Api\CommentRepositoryInterface $commentRepository,
        array $data = []
    ) {
        parent::__construct($context, $settingsHelper, $dateHelper, $dataHelper, $data);
        $this->commentRepository = $commentRepository;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("Amasty_Blog::sidebar/recentcomments.phtml");
        $this->addAmpTemplate("Amasty_Blog::amp/sidebar/recentcomments.phtml");
        $this->setRoute('display_recent_comments');
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getBlockHeader()
    {
        if (!$this->hasData('header_text')) {
            $this->setData('header_text', __('Recent Comments'));
        }

        return $this->getData('header_text');
    }

    /**
     * @return \Amasty\Blog\Model\ResourceModel\Comments\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCommentsCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->commentRepository->getRecentComments();
            $this->collection->setPageSize($this->getCommentsLimit());
        }

        return $this->collection;
    }

    /**
     * Get show thesis
     *
     * @return bool
     */
    public function needShowThesis()
    {
        return (bool)$this->getSettingsHelper()->getRecentCommentsDisplayShort();
    }

    /**
     * Get show date
     *
     * @return bool
     */
    public function needShowDate()
    {
        if (!$this->hasData('display_date')) {
            $this->setData('display_date', $this->getSettingsHelper()->getRecentCommentsDisplayDate());
        }

        return (bool)$this->getData('display_date');
    }

    /**
     * @return string
     */
    public function getCommentsLimit()
    {
        if (!$this->hasData('comments_limit')) {
            $this->setData('comments_limit', $this->getSettingsHelper()->getCommentsLimit());
        }

        return $this->getData('comments_limit');
    }
}
