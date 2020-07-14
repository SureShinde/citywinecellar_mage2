<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block;

use Magento\Widget\Block\BlockInterface;

class Featured extends \Magento\Framework\View\Element\Template implements BlockInterface
{
    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    private $collection;

    /**
     * @var \Amasty\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var string
     */
    protected $_template = 'Amasty_Blog::featured.phtml';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Blog\Api\PostRepositoryInterface $postRepository,
        array $data = []
    ) {
        $this->postRepository = $postRepository;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        return strpos($this->getRequest()->getPathInfo(), '/amp/') === false ? parent::toHtml() : '';
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $collection = $this->postRepository->getFeaturedPosts($this->storeManager->getStore()->getId());
            $this->collection = $collection;
        }

        return $this->collection;
    }
}
