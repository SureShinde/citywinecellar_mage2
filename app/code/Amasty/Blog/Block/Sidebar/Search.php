<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Sidebar;

class Search extends AbstractClass
{
    /**
     * @var \Amasty\Blog\Model\UrlResolver
     */
    private $urlResolver;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Blog\Helper\Settings $settingsHelper,
        \Amasty\Blog\Helper\Date $dateHelper,
        \Amasty\Blog\Helper\Data $dataHelper,
        \Amasty\Blog\Model\UrlResolver $urlResolver,
        array $data = []
    ) {
        parent::__construct($context, $settingsHelper, $dateHelper, $dataHelper, $data);
        $this->urlResolver = $urlResolver;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("Amasty_Blog::sidebar/search.phtml");
        $this->addAmpTemplate("Amasty_Blog::amp/sidebar/search.phtml");
        $this->setRoute('display_search');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getBlockHeader()
    {
        return __("Search The Blog");
    }

    /**
     * @return string
     */
    public function getSearchUrl()
    {
        return $this->urlResolver->setStoreId($this->getStoreId())->getSearchPageUrl();
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->stripTags($this->getRequest()->getParam('query'));
    }

    /**
     * @return string
     */
    public function getAmpSearchUrl()
    {
        return str_replace(['https:', 'http:'], '', $this->getSearchUrl());
    }
}
