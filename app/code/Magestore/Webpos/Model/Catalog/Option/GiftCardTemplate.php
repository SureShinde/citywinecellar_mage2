<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog\Option;
/**
 * Class GiftCardTemplate
 * @package Magestore\Webpos\Model\Catalog\Option
 */
class GiftCardTemplate extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateInterface
{
    /**
     * Get template id
     *
     * @return int|null
     */
    public function getTemplateId(){
        return $this->getData(self::TEMPLATE_ID);
    }
    /**
     * Set template id
     *
     * @param int $templateId
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateInterface
     */
    public function setTemplateId($templateId){
        return $this->setData(self::TEMPLATE_ID, $templateId);
    }
    /**
     * Get images
     *
     * @return string[]
     */
    public function getImages(){
        return $this->getData(self::IMAGES);
    }
    /**
     * Set images
     *
     * @param string[] $images
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateInterface
     */
    public function setImages($images){
        return $this->setData(self::IMAGES, $images);
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
        \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}