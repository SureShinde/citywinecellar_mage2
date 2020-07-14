<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\Barcode;

class BarcodeService implements BarcodeServiceInterface
{
    
    /**
     * Generate source of barcode image
     * 
     * @param string $barcodeString
     * @param array $config
     * 
     * @return string
     */
    public function getBarcodeSource($barcodeString, $config = [])
    {
        $symbology = isset($config['symbology']) ? $config['symbology'] : self::SYMBOLOGY;
        $fontSize = isset($config['font_size']) ? $config['font_size'] : self::FONT_SIZE;
        $height = isset($config['height']) ? $config['height'] : self::HEIGHT;
        $width = isset($config['width']) ? $config['width'] : self::WIDTH;
        $imageType = isset($config['image_type']) ? $config['image_type'] : self::IMAGE_TYPE;
        $fontSize = isset($config['font_size']) ? $config['font_size'] : self::FONT_SIZE;

        $barcodeOptions = [
            'text' => $barcodeString,
            'fontSize' => $fontSize
        ];
        $rendererOptions = [
            'width' => $width,
            'height' => $height,
            'imageType' => $imageType
        ];

        /* check if install barcode module */
        if(class_exists('\Zend_Barcode')) {
            $source = \Zend_Barcode::factory(
                $symbology, 'image', $barcodeOptions, $rendererOptions
            );
        }else if(class_exists('\Zend\Barcode\Barcode')){
            $source = \Zend\Barcode\Barcode::factory(
                $symbology, 'image', $barcodeOptions, $rendererOptions
            );
        }else{
            return false;
        }
            
        ob_start();
        imagepng($source->draw());
        $barcode = ob_get_contents();
        ob_end_clean();        

        return $barcode;     
    }
}