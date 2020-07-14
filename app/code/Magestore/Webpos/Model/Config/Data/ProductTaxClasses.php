<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Config\Data;
/**
 * Class SystemConfig
 */
class ProductTaxClasses extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Config\ProductTaxClassesInterface
{
    /**
     * Get label
     *
     * @api
     * @return string
     */
    public function getLabel() {
        return $this->getData(self::LABEL);
    }

    /**
     * Set label
     *
     * @api
     * @param string $label
     * @return $this
     */
    public function setLabel($label) {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * Get value
     *
     * @api
     * @return string
     */
    public function getValue() {
        return $this->getData(self::VALUE);
    }

    /**
     * Set value
     *
     * @api
     * @param string $value
     * @return $this
     */
    public function setValue($value) {
        return $this->setData(self::VALUE, $value);
    }
}
