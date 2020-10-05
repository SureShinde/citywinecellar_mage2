<?php

namespace Laconica\Catalog\Plugin\Block\Product\View;

class Gallery
{
    /**
     * @var \Laconica\Catalog\Helper\Config $configsHelper
     */
    private $configsHelper;

    public function __construct(
        \Laconica\Catalog\Helper\Config $configsHelper
    ) {
        $this->configsHelper = $configsHelper;
    }

    public function afterGetGalleryImages(
      \Magento\Catalog\Block\Product\View\Gallery $subject,
      $result
    ) {
        $product = $subject->getProduct();

        if ($result->getSize() <= 0 || !$product || !$this->configsHelper->isAltReplaceEnabled()) {
            return $result;
        }

        $productName = $product->getName();

        foreach ($result as $item) {
            $item->setData('label', $productName);
        }

        return $result;
    }
}