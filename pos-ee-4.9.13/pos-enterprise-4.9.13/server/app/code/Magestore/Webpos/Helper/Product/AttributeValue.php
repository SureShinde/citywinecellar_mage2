<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper\Product;

class AttributeValue extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $listAttributesTypeHasOptions = [
        'select',
        'multiselect',
        'swatch_visual',
        'swatch_text'
    ];

    /**
     * get default value for attribute
     *
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute
     * @return string
     */
    public function getDefaultValueAttribute($attribute) {
        $functionName = ucwords(str_replace('_', ' ', $attribute->getFrontendInput()));
        if(!$functionName) {
            return false;
        }
        $functionName = 'defaultType' . str_replace(' ', '', $functionName);
        if(in_array($attribute->getFrontendInput(), $this->listAttributesTypeHasOptions)) {
            $options = $attribute->getOptions();
            usort($options, [$this, 'sortArray']);
            return $this->$functionName($options);
        } else {
            return $this->$functionName();
        }
    }

    /**
     * default value for type text
     *
     * @return string
     */
    public function defaultTypeText() {
        return 'pwa_default_text_field_value';
    }

    /**
     * default value for type text area
     *
     * @return string
     */
    public function defaultTypeTextarea() {
        return 'pwa_default_text_field_area';
    }

    /**
     * default value for type date
     *
     * @return string
     */
    public function defaultTypeDate() {
        return (new \DateTime())->format('Y-m-d');
    }

    /**
     * default value for type boolean
     *
     * @return int
     */
    public function defaultTypeBoolean() {
        return \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES;
    }

    /**
     * default value for type multi select
     *
     * @param $options
     * @return mixed
     */
    public function defaultTypeMultiselect($options) {
        return $options[0]['value'];
    }

    /**
     * default value for type select
     *
     * @param $options
     * @return mixed
     */
    public function defaultTypeSelect($options) {
        return $options[0]['value'];
    }

    /**
     * default value for type price
     *
     * @return int
     */
    public function defaultTypePrice() {
        return 999999;
    }

    /**
     * default value for type swatch visual
     *
     * @param $options
     * @return mixed
     */
    public function defaultTypeSwatchVisual($options) {
        return $options[0]['value'];
    }

    /**
     * default value for type swatch text
     *
     * @param $options
     * @return mixed
     */
    public function defaultTypeSwatchText($options) {
        return $options[0]['value'];
    }

    /**
     * function sort order
     * @param $a
     * @param $b
     * @return int
     */
    public function sortArray($a, $b) {
        if($a['value'] > $b['value']) {
            return -1;
        } else {
            return 1;
        }
    }
}
