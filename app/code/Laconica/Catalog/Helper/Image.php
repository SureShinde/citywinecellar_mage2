<?php

namespace Laconica\Catalog\Helper;

class Image extends \Magento\Catalog\Helper\Image
{
    /**
     * @var Config $configsHelper
     */
    private $configsHelper;

    public function __construct(
        \Laconica\Catalog\Helper\Config $configsHelper,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\ImageFactory $productImageFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Magento\Catalog\Model\View\Asset\PlaceholderFactory $placeholderFactory = null
    ) {
        parent::__construct($context, $productImageFactory, $assetRepo, $viewConfig, $placeholderFactory);
        $this->configsHelper = $configsHelper;
    }

    public function getLabel() {
        if ($this->configsHelper->isAltReplaceEnabled()) {
            return $this->_product->getName();
        }
        return parent::getLabel();
    }
}