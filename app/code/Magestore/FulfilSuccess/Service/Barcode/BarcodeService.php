<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\Barcode;

/**
 * Service BarcodeService
 */
class BarcodeService implements BarcodeServiceInterface
{
    /**
     * Generate source of barcode image
     *
     * @param string $barcodeString
     * @param array $config
     * @return string
     */
    public function getBarcodeSource($barcodeString, $config = [])
    {
        $symbology = isset($config['symbology']) ? $config['symbology'] : self::SYMBOLOGY;
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

        /* Check if install barcode module */
        if (class_exists(\Zend_Barcode::class)) {
            $source = \Zend_Barcode::factory(
                $symbology,
                'image',
                $barcodeOptions,
                $rendererOptions
            );
        } elseif (class_exists(\Zend\Barcode\Barcode::class)) {
            $source = \Zend\Barcode\Barcode::factory(
                $symbology,
                'image',
                $barcodeOptions,
                $rendererOptions
            );
        } else {
            return false;
        }

        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        ob_start();
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        imagepng($source->draw());
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $barcode = ob_get_contents();
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        ob_end_clean();

        return $barcode;
    }
}
