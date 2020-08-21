<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Block\Barcode\Container;

use Magestore\BarcodeSuccess\Model\Source\MeasurementUnit;
use Magestore\BarcodeSuccess\Model\Source\TemplateType;

/**
 * Class Template
 *
 * Used to create template
 */
class Template extends \Magestore\BarcodeSuccess\Block\Barcode\Container
{
    /**
     * Check Zend Barcode module is installed
     *
     * @return bool
     */
    public function isZendBarcodeInstalled()
    {
        return $this->helper->isZendBarcodeInstalled();
    }

    /**
     * Get Barcode Configuration url
     *
     * @return mixed
     */
    public function getBarcodeSetupGuideUrl()
    {
        return $this->helper->getBarcodeSetupGuideUrl();
    }

    /**
     * Get barcode
     *
     * @return array
     */
    public function getBarcodes()
    {
        $barcodes = [];
        $datas = $this->getData('barcodes');
        if ($datas) {
            $template = $this->getTemplateData();
            foreach ($datas as $data) {
                if (empty($data['qty'])) {
                    $data['qty'] = $template['label_per_row'];
                }
                for ($i = 1; $i <= $data['qty']; $i++) {
                    $barcodes[] = $this->getBarcodeSource($data);
                }
            }
        }
        return $barcodes;
    }

    /**
     * Get barcode source
     *
     * @param array $data
     * @return mixed
     */
    public function getBarcodeSource($data)
    {
        $source = "";
        if ($data) {
            $template = $this->getTemplateData();
            $type = $template['symbology'];
            $databarcode = $data['barcode'];
            if ($type == 'ean13') {
                $databarcode = substr($data['barcode'], 0, -1);
            }
            $barcodeOptions = [
                'text' => $databarcode,
                'fontSize' => $template['font_size']
            ];
            $rendererOptions = [
                //'width' => '198',
                'height' => '0',
                'imageType' => 'png'
            ];

            // phpstan:ignore
            $source = \Zend\Barcode\Barcode::factory(
                $type,
                'image',
                $barcodeOptions,
                $rendererOptions
            );

            if (isset($template['product_attribute_show_on_barcode'])) {
                $attributeDatas = $this->getAttributeSoucre($data, $template['product_attribute_show_on_barcode']);
            } else {
                $attributeDatas = [];
            }
        }
        $result['attribute_data'] = $attributeDatas;
        $result['barcode_source'] = $source;
        return $result;
    }

    /**
     * Get attribute source
     *
     * @param array $data
     * @param array $attributes
     * @return array
     * phpcs:disable Generic.Metrics.NestingLevel
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getAttributeSoucre($data, $attributes)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (isset($data['product_id']) && $data['product_id']) {
            $product_id = $data['product_id'];
        } else {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $prdCollection */
            $prdCollection = $objectManager->create(\Magento\Catalog\Model\Product::class)->getCollection();
            $firstProduct = $prdCollection->getFirstItem();
            if ($firstProduct) {
                $product_id = $firstProduct->getId();
            } else {
                return [];
            }
        }

        $attributeArray = [];
        if ($product_id && $attributes && $attributes != '') {
            if (is_array($attributes)) {
                $array = $attributes;
            } else {
                $array = explode(',', $attributes);
            }
            /** @var \Magento\Catalog\Model\Product $product */
            $prod = $objectManager->create(\Magento\Catalog\Model\Product::class)->load($product_id);
            foreach ($array as $key) {
                if ($key && ($text = ($prod->getData($key) ? $prod->getData($key) : $prod->getData($key)))) {
                    if (($key === 'sku') || ($key === 'name')) {
                        $attributeArray[] = (is_numeric($text) ? (int)$text : $text);
                    } elseif (($key === 'price')) {
                        $price = $text;
                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $priceHelper = $objectManager->create(\Magento\Framework\Pricing\Helper\Data::class);
                        $formattedPrice = $priceHelper->currency($price, true, false);
                        $attributeArray[] = '[' . $key . '] ' . $formattedPrice;
                    } else {
                        $attributeModel = $objectManager->create(\Magento\Eav\Model\Entity\Attribute::class)
                            ->loadByCode('catalog_product', $key);
                        if ($attributeModel->getId()) {
                            if ($attributeModel->usesSource()) {
                                foreach ($attributeModel->getOptions() as $option) {
                                    if ($option->getValue() == $text) {
                                        $attributeArray[] = '[' . $key . '] ' . $option->getLabel();
                                    }
                                }
                            } else {
                                $attributeArray[] = '[' . $key . '] ' . (is_numeric($text) ? (int)$text : $text);
                            }
                        }
                    }
                }
            }
        }
        return $attributeArray;
    }

    /**
     * Get template data
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getTemplateData()
    {
        $data = [];
        if ($this->getData('template_data')) {
            $data = $this->getData('template_data');
        }
        if (empty($data['font_size'])) {
            $data['font_size'] = '24';
        }
        if (empty($data['label_per_row'])) {
            $data['label_per_row'] = '1';
        }
        if (empty($data['measurement_unit'])) {
            $data['measurement_unit'] = MeasurementUnit::MM;
        }
        if (empty($data['paper_height'])) {
            $data['paper_height'] = '30';
        }
        if (empty($data['paper_width'])) {
            $data['paper_width'] = '100';
        }
        if (empty($data['label_height'])) {
            $data['label_height'] = '30';
        }
        if (empty($data['label_width'])) {
            $data['label_width'] = '100';
        }
        if (empty($data['left_margin'])) {
            $data['left_margin'] = '0';
        }
        if (empty($data['right_margin'])) {
            $data['right_margin'] = '0';
        }
        if (empty($data['bottom_margin'])) {
            $data['bottom_margin'] = '0';
        }
        if (empty($data['top_margin'])) {
            $data['top_margin'] = '0';
        }
        return $data;
    }

    /**
     * Is jewelry
     *
     * @return bool
     */
    public function isJewelry()
    {
        $template = $this->getTemplateData();
        return ($template['type'] == TemplateType::JEWELRY) ? true : false;
    }
}
