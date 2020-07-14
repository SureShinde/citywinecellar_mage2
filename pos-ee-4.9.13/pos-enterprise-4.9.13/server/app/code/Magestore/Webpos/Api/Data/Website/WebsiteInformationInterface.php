<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Website;

/**
 * Interface WebsiteInformationInterface
 * @package Magestore\Webpos\Api\Data\Website
 */
interface WebsiteInformationInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     *
     */
    const LOGO_URL = "logo_url";

    /**
     * Get logo url
     *
     * @api
     * @return string
     */
    public function getLogoUrl();

    /**
     * Set logo url
     *
     * @api
     * @param string $logoUrl
     * @return WebsiteInformationInterface
     */
    public function setLogoUrl($logoUrl);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Website\WebsiteInformationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Website\WebsiteInformationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Website\WebsiteInformationExtensionInterface $extensionAttributes
    );

}
