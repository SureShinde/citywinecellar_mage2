<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml;

use Amasty\Blog\Helper\Url;
use Magento\Framework\App\Request\DataPersistorInterface;

abstract class Posts extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Url
     */
    private $urlHelper;

    /**
     * @var \Amasty\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezone;

    /**
     * @var \Amasty\Blog\Model\ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var \Amasty\Blog\Model\BlogRegistry
     */
    private $blogRegistry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Amasty\Blog\Helper\Url $urlHelper,
        \Amasty\Blog\Api\PostRepositoryInterface $postRepository,
        DataPersistorInterface $dataPersistor,
        \Amasty\Blog\Model\BlogRegistry $blogRegistry,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Psr\Log\LoggerInterface $logger,
        \Amasty\Blog\Model\ImageProcessor $imageProcessor
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->urlHelper = $urlHelper;
        $this->postRepository = $postRepository;
        $this->dataPersistor = $dataPersistor;
        $this->timezone = $timezone;
        $this->logger = $logger;
        $this->imageProcessor = $imageProcessor;
        $this->blogRegistry = $blogRegistry;
    }

    /**
     * @return \Amasty\Blog\Model\ImageProcessor
     */
    public function getImageProcessor()
    {
        return $this->imageProcessor;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Blog::posts');
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return DataPersistorInterface
     */
    public function getDataPersistor()
    {
        return $this->dataPersistor;
    }

    /**
     * @return \Amasty\Blog\Model\BlogRegistry
     */
    public function getRegistry()
    {
        return $this->blogRegistry;
    }

    /**
     * @return \Amasty\Blog\Helper\Url
     */
    public function getUrlHelper()
    {
        return $this->urlHelper;
    }

    /**
     * @return \Amasty\Blog\Api\PostRepositoryInterface
     */
    public function getPostRepository()
    {
        return $this->postRepository;
    }

    /**
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function getPageFactory()
    {
        return $this->resultPageFactory;
    }

    /**
     * @return \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    public function getTimezone()
    {
        return $this->timezone;
    }
}
