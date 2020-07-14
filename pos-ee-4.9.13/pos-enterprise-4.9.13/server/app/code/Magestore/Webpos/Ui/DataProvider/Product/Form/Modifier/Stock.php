<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Ui\AllowedProductTypes;


class Stock extends AbstractModifier
{
    /**
     * @var array
     */
    private $modifiers = [];

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var AllowedProductTypes
     */
    protected $allowedProductTypes;


    /**
     * Stock constructor.
     * @param LocatorInterface $locator
     * @param ObjectManagerInterface $objectManager
     * @param AllowedProductTypes $allowedProductTypes
     * @param array $modifiers
     */
    public function __construct(
        LocatorInterface $locator,
        ObjectManagerInterface $objectManager,
        AllowedProductTypes $allowedProductTypes,
        array $modifiers = []
    ) {
        $this->locator = $locator;
        $this->objectManager = $objectManager;
        $this->allowedProductTypes = $allowedProductTypes;
        $this->modifiers = $modifiers;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($this->allowedProductTypes->isAllowedProductType($this->locator->getProduct())) {
            foreach ($this->modifiers as $modifierClass) {
                /** @var ModifierInterface $bundleModifier */
                $modifier = $this->objectManager->get($modifierClass);
                if (!$modifier instanceof ModifierInterface) {
                    throw new \InvalidArgumentException(__(
                        'Type %1 is not an instance of %2',
                        $modifierClass,
                        ModifierInterface::class
                    ));
                }
                $data = $modifier->modifyData($data);
            }
        }
        return $data;
    }


    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if ($this->allowedProductTypes->isAllowedProductType($this->locator->getProduct())) {
            foreach ($this->modifiers as $modifierClass) {
               
                /** @var ModifierInterface $bundleModifier */
                $modifier = $this->objectManager->get($modifierClass);
                if (!$modifier instanceof ModifierInterface) {
                    throw new \InvalidArgumentException(__(
                        'Type %1 is not an instance of %2',
                        $modifierClass,
                        ModifierInterface::class
                    ));
                }
                $meta = $modifier->modifyMeta($meta);
            }
        }

        return $meta;
    }
}
