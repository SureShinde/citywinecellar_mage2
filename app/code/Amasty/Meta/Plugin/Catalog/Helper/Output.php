<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Plugin\Catalog\Helper;

class Output
{

    /**
     * @var \Amasty\Meta\Helper\Data
     */
    private $data;

    public function __construct(
        \Amasty\Meta\Helper\Data $data
    ) {
        $this->data = $data;
    }

    public function aroundProductAttribute(
        $subject,
        \Closure $proceed,
        $product,
        $attributeHtml,
        $attributeName
    ) {
        $result = $proceed($product, $attributeHtml, $attributeName);
        $replaced = false;

        if ($attributeName == 'short_description') {
            $replaced = $this->data->getReplaceData('short_description');
        } elseif ($attributeName == 'description') {
            $replaced = $this->data->getReplaceData('description');
        }
        if ($replaced) {
            $result = $replaced;
        }

        return $result;
    }

    public function aroundCategoryAttribute(
        $subject,
        \Closure $proceed,
        $product,
        $attributeHtml,
        $attributeName
    ) {
        $replaced = false;
        $result = $proceed($product, $attributeHtml, $attributeName);

        switch ($attributeName) {
            case 'short_description':
            case 'description':
                $replaced = $this->data->getReplaceData($attributeName);

                if ($replaced) {
                    $result = $replaced;
                }

                break;
            case 'image':
                $result = preg_replace(
                    '@(alt=["\'])[^"\']*(["\'])@s',
                    '${1}' . $this->data->getReplaceData('image_alt') . '${2}',
                    $result
                );
                break;
        }

        return $result;
    }
}
