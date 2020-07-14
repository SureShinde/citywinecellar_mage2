<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Config;

/**
 * @api
 */
interface ProductTaxClassesInterface
{
    const VALUE = 'value';
    const LABEL = 'label';

    /**
     * Get label
     * 
     * @return string
     */
    public function getLabel();

    /**
     * Set label
     *
     * @api
     * @param string $label
     * @return ProductTaxClassesInterface
     */
    public function setLabel($label);
    
    /**
     * Get value
     *
     * @api
     * @return string
     */
    public function getValue();

    /**
     * Set value
     *
     * @api
     * @param string $value
     * @return ProductTaxClassesInterface
     */
    public function setValue($value);
}
