<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\App\RequestInterface;
use Magestore\Giftvoucher\Model\Product\Type\Giftvoucher as GiftvoucherProduct;

/**
 * Class adds Downloadable collapsible panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Giftvoucher extends AbstractModifier
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * Giftvoucher constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param RequestInterface $request
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        RequestInterface $request,
        \Magento\Framework\Module\Manager $moduleManager
    )
    {
        $this->objectManager = $objectManager;
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->request = $request;
        $this->moduleManager = $moduleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $productType = $this->request->getParam('type');
        $product = $this->locator->getProduct();
        if ($productType == GiftvoucherProduct::GIFT_CARD_TYPE ||
            $product->getTypeId() == GiftvoucherProduct::GIFT_CARD_TYPE
        ) {
            if ($this->moduleManager->isEnabled('Magento_InventoryCatalogApi')) {
                /** @var \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface $isSingleSourceMode */
                $isSingleSourceMode = $this->objectManager->get(
                    'Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface'
                );
                if (!$isSingleSourceMode->execute()) {
                    $meta = array_replace_recursive(
                        $meta,
                        ['advanced_inventory_modal' => [
                            'children' => [
                                'stock_data' => [
                                    'children' => [
                                        'qty' => [
                                            'arguments' => [
                                                'data' => [
                                                    'config' => [
                                                        'visible' => 0,
                                                        'imports' => ''
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]]
                    );
                }
            }
        }

        /*echo "<pre>";
        foreach ($meta as $key => $value) {
            var_dump($key);
        }
        var_dump($meta);
        die;*/
        return $meta;
    }
}
