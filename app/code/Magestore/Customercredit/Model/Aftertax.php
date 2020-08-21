<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */


/**
 * Customercredit Model
 *
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
namespace Magestore\Customercredit\Model;

/**
 * Class Aftertax
 *
 * After tax model
 */
class Aftertax
{
    /**
     * Get Option Array
     *
     * @return array
     */
    public function getOptionArray()
    {
        return [
            0 => __('Before tax'),
            1 => __('After tax'),
        ];
    }

    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getOptionArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $options;
    }
}
