<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Catalog\Option;

/**
 * Interface ConfigOptionsInterface
 */
interface ConfigOptionsInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = 'id';
    const CODE = 'code';
    const LABEL = 'label';
    const OPTIONS = 'options';
    const POSITION = 'position';

    /**
     * Get Id
     *
     * @return int|null
     */
    public function getId();
    /**
     * Set Id
     *
     * @param int $id
     * @return ConfigOptionsInterface
     */
    public function setId($id);

    /**
     * Get Code
     *
     * @return string|null
     */
    public function getCode();
    /**
     * Set Code
     *
     * @param string $code
     * @return ConfigOptionsInterface
     */
    public function setCode($code);

    /**
     * Get Label
     *
     * @return string|null
     */
    public function getLabel();
    /**
     * Set Label
     *
     * @param string $label
     * @return ConfigOptionsInterface
     */
    public function setLabel($label);

    /**
     * Get Options
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\OptionInterface[]
     */
    public function getOptions();
    /**
     * Set Options
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\OptionInterface[] $options
     * @return ConfigOptionsInterface
     */
    public function setOptions($options);

    /**
     * Get Position
     *
     * @return int|null
     */
    public function getPosition();
    /**
     * Set Position
     *
     * @param int $position
     * @return ConfigOptionsInterface
     */
    public function setPosition($position);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\ConfigOptionsExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\ConfigOptionsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Catalog\Option\ConfigOptionsExtensionInterface $extensionAttributes
    );
}