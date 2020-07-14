<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magestore\BarcodeSuccess\Model\Source\MeasurementUnit;
use Magestore\BarcodeSuccess\Model\Source\Symbology;
use Magestore\BarcodeSuccess\Model\Source\Status;

/**
 * Class TemplateType
 * @package Magestore\BarcodeSuccess\Model\Source\TemplateType
 */

class TemplateType implements OptionSourceInterface
{

    const STANDARD = 'standard';
    const A4 = 'a4';
    const JEWELRY = 'jewelry';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = [
            self::STANDARD => __('Standard Barcode'),
            self::A4 => __('A4 Barcode'),
            self::JEWELRY => __('Jewelry Barcode')
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
    public function getDefaultStandard(){
        $data = [];
        $data["type"] = self::STANDARD;
        $data["status"] = Status::ACTIVE;
        $data["measurement_unit"] = MeasurementUnit::MM;
        $data["symbology"] = Symbology::CODE128;
        $data["name"] = __("Standard");
        $data["label_per_row"] = "3";
        $data["paper_width"] = "109";
        $data["paper_height"] = "24";
        $data["label_width"] = "35";
        $data["label_height"] = "22";
        $data["font_size"] = "16";
        $data["top_margin"] = "1";
        $data["left_margin"] = "1";
        $data["bottom_margin"] = "1";
        $data["right_margin"] = "1";
        return $data;
    }
    public function getDefaultA4(){
        $data = [];
        $data["type"] = self::A4;
        $data["status"] = Status::ACTIVE;
        $data["measurement_unit"] = MeasurementUnit::MM;
        $data["symbology"] = Symbology::CODE128;
        $data["name"] = __("A4");
        $data["label_per_row"] = "4";
        $data["paper_width"] = "210";
        $data["paper_height"] = "20";
        $data["label_width"] = "48.25";
        $data["label_height"] = "16";
        $data["font_size"] = "16";
        $data["top_margin"] = "2";
        $data["left_margin"] = "2";
        $data["bottom_margin"] = "2";
        $data["right_margin"] = "2";
        return $data;
    }
    public function getDefaultJewelry(){
        $data = [];
        $data["type"] = self::JEWELRY;
        $data["status"] = Status::ACTIVE;
        $data["measurement_unit"] = MeasurementUnit::MM;
        $data["symbology"] = Symbology::CODE128;
        $data["name"] = __("Jewelry");
        $data["label_per_row"] = "1";
        $data["paper_width"] = "88";
        $data["paper_height"] = "15";
        $data["label_width"] = "25";
        $data["label_height"] = "11";
        $data["font_size"] = "24";
        $data["top_margin"] = "1";
        $data["left_margin"] = "1";
        $data["bottom_margin"] = "1";
        $data["right_margin"] = "1";
        return $data;
    }

    public function getDefaultData($type = ""){
        switch ($type){
            case self::STANDARD:
                $data = $this->getDefaultStandard();
                break;
            case self::A4:
                $data = $this->getDefaultA4();
                break;
            case self::JEWELRY:
                $data = $this->getDefaultJewelry();
                break;
            default:
                $data = [];
                $data[self::STANDARD] = $this->getDefaultStandard();
                $data[self::A4] = $this->getDefaultA4();
                $data[self::JEWELRY] = $this->getDefaultJewelry();
                break;
        }
        return $data;
    }
}
