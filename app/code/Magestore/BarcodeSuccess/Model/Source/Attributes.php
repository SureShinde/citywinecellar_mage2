<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Model\Source;

use Magento\CatalogSearch\Model\Advanced;
use Magento\Framework\Data\OptionSourceInterface;
use Magestore\BarcodeSuccess\Model\Source\MeasurementUnit;
use Magestore\BarcodeSuccess\Model\Source\Symbology;
use Magestore\BarcodeSuccess\Model\Source\Status;

/**
 * Class TemplateType
 *
 * Attributes options
 */
class Attributes implements OptionSourceInterface
{
    /**
     * @var Advanced
     */
    protected $_catalogSearchAdvanced;

    /**
     * Attributes constructor.
     *
     * @param Advanced $catalogSearchAdvanced
     */
    public function __construct(
        Advanced $catalogSearchAdvanced
    ) {
        $this->_catalogSearchAdvanced = $catalogSearchAdvanced;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->getAttributeOptions();
        $options = [];
        $options[] = ['value' => 'sku', 'label' => 'SKU'];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    /**
     * Get attribute options
     *
     * @return array
     */
    public function getAttributeOptions()
    {
        $availableOptions = [];
        $attributes = $this->_catalogSearchAdvanced->getAttributes();
        foreach ($attributes as $attribute) {
            if (($attribute->getAttributeCode() === 'sku') || ($attribute->getAttributeCode() === 'description') ||
                ($attribute->getAttributeCode() === 'short_description')) {
                continue;
            }
            $availableOptions[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }
        return $availableOptions;
    }
}
