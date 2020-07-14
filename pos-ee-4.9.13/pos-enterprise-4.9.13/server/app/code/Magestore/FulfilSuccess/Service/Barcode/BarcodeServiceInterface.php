<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\Barcode;


interface BarcodeServiceInterface
{
    /* default barcode config value */
    const SYMBOLOGY = 'code128';
    const FONT_SIZE = 16;
    const HEIGHT = 0;
    const WIDTH = 0;
    const IMAGE_TYPE = 'png';
    
    /**
     * Generate source of barcode image
     * 
     * @param string $barcodeString
     * @param array $config
     * 
     * @return string
     */
    public function getBarcodeSource($barcodeString, $config = []);
    
}