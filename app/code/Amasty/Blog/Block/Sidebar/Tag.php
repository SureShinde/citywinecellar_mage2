<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Sidebar;

class Tag extends AbstractClass
{
    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    private $collection;

    /**
     * @var \Amasty\Blog\Api\TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var int
     */
    private $postsCount = 0;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Blog\Helper\Settings $settingsHelper,
        \Amasty\Blog\Helper\Date $dateHelper,
        \Amasty\Blog\Helper\Data $dataHelper,
        \Amasty\Blog\Api\TagRepositoryInterface $tagRepository,
        array $data = []
    ) {
        parent::__construct($context, $settingsHelper, $dateHelper, $dataHelper, $data);
        $this->tagRepository = $tagRepository;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("Amasty_Blog::sidebar/tags.phtml");
        $this->addAmpTemplate('Amasty_Blog::amp/list/tags.phtml');
        $this->setRoute('use_tags');
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        return strpos($this->getRequest()->getPathInfo(), '/amp/') === false ? parent::toHtml() : '';
    }

    /**
     * @param int $storeId
     * @return \Amasty\Blog\Model\ResourceModel\Tag\Collection
     */
    public function getCollection($storeId = null)
    {
        if (!$this->collection) {
            $collection = $this->tagRepository->getActiveTags($storeId);
            $collection->setMinimalPostCountFilter($this->getSettingsHelper()->getTagsMinimalPostCount())
                ->setNameOrder();

            $this->collection = $collection;
        }

        return $this->collection;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPostsCount()
    {
        if (!$this->postsCount) {
            foreach ($this->getCollection() as $item) {
                $this->postsCount += $item->getPostCount();
            }
        }

        return $this->postsCount;
    }

    /**
     * @param $tag
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTagWeight($postsInTagCount)
    {
        $postsCount = $this->getPostsCount();

        return $postsCount ? (($postsInTagCount * 100) / $postsCount) : 0;
    }

    /**
     * @return bool
     */
    public function getMtEnabled()
    {
        return $this->getSettingsHelper()->getTagsMtEnabled();
    }

    /**
     * @return int
     */
    public function getMtHeight()
    {
        return $this->getSettingsHelper()->getTagsMtHeight();
    }

    /**
     * @return string
     */
    public function getMtTextColor()
    {
        return $this->getSettingsHelper()->getTagsMtTextcolor();
    }

    /**
     * @return string
     */
    public function getMtTextColor2()
    {
        return $this->getSettingsHelper()->getTagsMtTextcolor2();
    }

    /**
     * @return string
     */
    public function getMtHiColor()
    {
        return $this->getSettingsHelper()->getTagsMtHiColor();
    }

    /**
     * @param \Amasty\Blog\Model\Tag $tag
     * @return bool
     */
    public function isActive(\Amasty\Blog\Model\Tag $tag)
    {
        $result = false;
        if ($this->getRequest()->getModuleName() == "amblog"
            && $this->getRequest()->getControllerName() == "index"
            && $this->getRequest()->getActionName() == "tag"
            && $this->getRequest()->getParam('id') == $tag->getTagId()
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * @return $this|AbstractClass
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->wantAsserts()) {
            if ($this->getSettingsHelper()->getTagsMtEnabled()) {
                $this->getLayout()->createBlock(
                    \Magento\Framework\View\Element\Template::class,
                    '',
                    ['data' => ['template' => 'Amasty_Blog::sidebar/tags/js.phtml']]
                );
            }
        }

        return $this;
    }
}
