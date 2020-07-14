<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Catalog\Option;

/**
 * Interface GiftCardTemplateInterface
 */
interface GiftCardTemplateInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const TEMPLATE_ID = 'template_id';
    const IMAGES = 'images';
    /**
     * Get template id
     *
     * @return int|null
     */
    public function getTemplateId();
    /**
     * Set template id
     *
     * @param int $templateId
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateInterface
     */
    public function setTemplateId($templateId);
    /**
     * Get images
     *
     * @return string[]
     */
    public function getImages();
    /**
     * Set images
     *
     * @param string[] $images
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateInterface
     */
    public function setImages($images);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateExtensionInterface $extensionAttributes
    );

}