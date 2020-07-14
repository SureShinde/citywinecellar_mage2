<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Block\Adminhtml\Page;

use Amasty\Xlanding\Api\Data\PageInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Link extends \Magento\Backend\Block\Template
{
    const LABEL = 'Page Link';
    const HTML_ID = 'category-page-link';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    /**
     * @var \Amasty\Xlanding\Api\PageRepositoryInterface
     */
    private $pageRepository;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Xlanding\Api\PageRepositoryInterface $pageRepository,
        array $data = []
    ) {
        $this->setTemplate('form/field/link.phtml');
        $this->pageRepository = $pageRepository;
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __(self::LABEL);
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $category = $this->coreRegistry->registry('current_category');
        if ($category !== null) {
            try {
                $pageId = $category->getData(PageInterface::DYNAMIC_CATEGORY_PAGE_ID);
                $page = $this->pageRepository->getById($pageId);
                return sprintf('<a href="%s">%s</a>',
                    $this->getUrl('amasty_xlanding/page/edit', ['page_id' => $pageId]),
                    __('Edit Landing Page: %1', $page->getTitle())
                );
            } catch (NoSuchEntityException $e) {
                return '';
            }
        }
        return '';
    }

    /**
     * @return string
     */
    public function getId()
    {
        return self::HTML_ID;
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return $this->getId();
    }
}
