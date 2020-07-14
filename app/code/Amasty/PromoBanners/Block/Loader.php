<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */

namespace Amasty\PromoBanners\Block;

use Amasty\PromoBanners\Model\Rule;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Search\Model\QueryFactory;

class Loader extends Template
{
    const SEARCH_PAGE_URL = '/catalogsearch/result/';
    const DATA_SOURCE_URL = 'amasty_banners/banner/ajax';

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
    }

    public function getParams()
    {
        $context = [];

        /** @var \Magento\Catalog\Model\Product $product */
        if ($product = $this->registry->registry('current_product')) {
            $context['currentProduct'] = $product->getSku();
        } elseif ($category = $this->registry->registry('current_category')) {
            $context['currentCategory'] = $category->getId();
        } elseif ($this->_request->getPathInfo() == self::SEARCH_PAGE_URL) {
            $context['searchQuery'] = $this->escapeUrl($this->_request->getParam(QueryFactory::QUERY_VAR_NAME));
        }

        $params = [
            'dataUrl' => $this->getUrl(self::DATA_SOURCE_URL),
            'context' => $context,
            'injectorSectionId' => Rule::POS_AMONG_PRODUCTS
        ];

        return $params;
    }
}
