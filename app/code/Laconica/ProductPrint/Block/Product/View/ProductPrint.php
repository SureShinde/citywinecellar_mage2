<?php

namespace Laconica\ProductPrint\Block\Product\View;

use Magento\Catalog\Block\Product\View;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductPrint extends View
{

    public function getBarcode()
    {
        $product = $this->getProduct();
        $barnum = $product->getData('item_lookup_code');
        if ($barnum) {
            $generator = new BarcodeGeneratorPNG();
            return base64_encode($generator->getBarcode($barnum, $generator::TYPE_CODE_128));
        }
        return null;
    }
}
