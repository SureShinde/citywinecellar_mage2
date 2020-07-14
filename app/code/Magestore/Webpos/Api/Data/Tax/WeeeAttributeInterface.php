<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Tax;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * List of all weee attributes, their amounts, etc.., that product has
 * @api
 * @since 100.2.0
 */
interface WeeeAttributeInterface extends ExtensibleDataInterface
{
    const WEBSITE_ID = 'website_id';
    const COUNTRY = 'country';
    const STATE = 'state';
    const VALUE = 'value';
    const WEBSITE_VALUE = 'website_value';
    /**
     * @return int
     */
    public function getWebsiteId();

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @return int
     */
    public function getState();

    /**
     * @return float
     */
    public function getValue();

    /**
     * @return int
     */
    public function getWebsiteValue();
}
