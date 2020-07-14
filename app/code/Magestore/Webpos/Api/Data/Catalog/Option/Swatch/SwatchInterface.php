<?php

namespace Magestore\Webpos\Api\Data\Catalog\Option\Swatch;

interface SwatchInterface extends \Magento\Framework\Api\ExtensibleDataInterface {
    const SWATCH_ID = 'swatch_id';
    const OPTION_ID = 'option_id';
    const TYPE = 'type';
    const VALUE = 'value';

    /**
     * Get Swatch Id
     *
     * @return int|null
     */
    public function getSwatchId();	
    /**
     * Set Swatch Id
     *
     * @param int $swatchId
     * @return SwatchInterface
     */
    public function setSwatchId($swatchId);

    /**
     * Get Option Id
     *
     * @return int|null
     */
    public function getOptionId();	
    /**
     * Set Option Id
     *
     * @param int $optionId
     * @return SwatchInterface
     */
    public function setOptionId($optionId);

    /**
     * Get Type
     *
     * @return int|null
     */
    public function getType();	
    /**
     * Set Type
     *
     * @param int $type
     * @return SwatchInterface
     */
    public function setType($type);

    /**
     * Get Value
     *
     * @return string|null
     */
    public function getValue();	
    /**
     * Set Value
     *
     * @param string $value
     * @return SwatchInterface
     */
    public function setValue($value);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\Swatch\SwatchExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\Swatch\SwatchExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Catalog\Option\Swatch\SwatchExtensionInterface $extensionAttributes
    );
}