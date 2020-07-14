<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Symbology
 * @package Magestore\BarcodeSuccess\Model\Source
 */

class Symbology implements OptionSourceInterface
{

    const CODE128 = 'code128';
    const CODE25 = 'code25';
    const CODE25INTERLEAVED = 'code25interleaved';
    const CODE39 = 'code39';
    const EAN13 = 'ean13';
//    const EAN2 = 'ean2';
//    const EAN5 = 'ean5';
//    const EAN8 = 'ean8';
    const IDENTCODE = 'identcode';
    const ITF14 = 'itf14';
    const LEITCODE = 'leitcode';
//    const PLANET = 'planet';
//    const POSTNET = 'postnet';
    const ROYALMAIL = 'royalmail';
//    const UPCA = 'upca';
//    const UPCE = 'upce';
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = [
            self::CODE128 => __('Code-128'),
            self::CODE25 => __('Code-25'),
            self::CODE25INTERLEAVED => __('Interleaved 2 of 5'),
            self::CODE39 => __('Code-39'),
            self::EAN13 => __('Ean-13'),
//            self::EAN2 => __('Ean-2'),
//            self::EAN5 => __('Ean-5'),
//            self::EAN8 => __('Ean-8'),
            self::IDENTCODE => __('Identcode'),
            self::ITF14 => __('Itf14'),
            self::LEITCODE => __('Leitcode'),
//            self::PLANET => __('Planet'),
//            self::POSTNET => __('Postnet'),
            self::ROYALMAIL => __('Royalmail'),
//            self::UPCA => __('UPC-A'),
//            self::UPCE => __('UPC-E')
        ];
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
