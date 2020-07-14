<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Block;

use Amasty\PromoBanners\Block\Banner\ProductListing;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Amasty\PromoBanners\Model\Rule;

class Banner extends Template
{
    protected $_template = 'banner.phtml';

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $contentProcessor;

    public function __construct(
        Template\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $contentProcessor,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->contentProcessor = $contentProcessor;
    }

    /**
     * @var Rule
     */
    protected $banner;

    public function setBanner(Rule $banner)
    {
        $this->banner = $banner;
    }

    public function getBanner()
    {
        return $this->banner;
    }

    public function getBannerText()
    {
        switch ($this->banner->getBannerType()) {
            case Rule::TYPE_CMS:
                $blockName = $this->banner->getCmsBlock();
                if ($blockName) {
                    $blockHtml = $this->getLayout()
                        ->createBlock(\Magento\Cms\Block\Block::class)
                        ->setBlockId($blockName)
                        ->toHtml();

                    return $blockHtml;
                }
                break;
            case Rule::TYPE_IMAGE:
                $title = __($this->banner->getBannerTitle());
                $imageUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                    . 'amasty/ampromobanners/' . $this->banner->getBannerImg();

                $html = "<img src='$imageUrl' alt='$title' />";

                if ($link = $this->banner->getBannerLink()) {
                    $html = "<a href='$link'>$html</a>";
                }

                return $html;
            case Rule::TYPE_HTML:
            default:
                return $this->contentProcessor->getPageFilter()->filter($this->banner->getHtmlText());
        }

        return '';
    }

    public function getBannerProducts()
    {
        return $this->banner->getData('product_collection') ?: [];
    }

    /**
     * @return array
     */
    public function getBannerShowProducts()
    {
        return $this->banner->getData('show_products') ?: [];
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $products = [];
        if ($this->getBannerShowProducts()) {
            $products = $this->getBannerProducts();
        }

        $listingBlock = $this->getLayout()
            ->createBlock(ProductListing::class)
            ->setData('products', $products);

        $this->setChild('product_listing', $listingBlock);

        return parent::_toHtml();
    }
}
