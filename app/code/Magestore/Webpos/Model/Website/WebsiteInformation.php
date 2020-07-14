<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Website;

class WebsiteInformation extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Website\WebsiteInformationInterface
{

    /**
     * Get logo url
     *
     * @api
     * @return string
     */
    public function getLogoUrl() {
        return $this->getData(self::LOGO_URL);
    }

    /**
     * Set logo url
     *
     * @api
     * @param string $logoUrl
     * @return $this
     */
    public function setLogoUrl($logoUrl) {
        return $this->setData(self::LOGO_URL, $logoUrl);
    }
    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Website\WebsiteInformationExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
