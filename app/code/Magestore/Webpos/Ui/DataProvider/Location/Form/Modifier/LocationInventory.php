<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\DataProvider\Location\Form\Modifier;

use Magento\Framework\UrlInterface;
use Magento\Framework\Phrase;
use Magento\Ui\Component\Form;

/**
 * Class InventoryLocation
 * @package Magestore\Webpos\Ui\DataProvider\Location\Form\Modifier
 */
class LocationInventory implements \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Magestore\Webpos\Model\Location\Location
     */
    protected $currentLocation;

    protected $_groupLabel = 'Location Inventory';
    protected $_sortOrder = 20;
    protected $_groupContainer = 'location_inventory';

    /**
     * InventoryLocation constructor.
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        UrlInterface $urlBuilder
    )
    {
        $this->registry = $registry;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $this->loadedData = [];
        $location = $this->getCurrentLocation();
        if ($location) {
            $locationData = $location->getData();
            $this->loadedData[$location->getId()] = $locationData;
        }
        return $this->loadedData;
    }

    /**
     * get visible
     *
     * @param
     * @return
     */
    public function getOpened()
    {
        $locationId = $this->getCurrentLocation()->getId();
        if ($locationId)
            return true;
        return false;
    }

    /**
     * get visible
     *
     * @param
     * @return
     */
    public function getVisible()
    {
        $locationId = $this->getCurrentLocation()->getId();
        if (!$locationId)
            return false;
        return true;
    }

    /**
     * modify data
     *
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Get current location
     *
     * @return \Magestore\Webpos\Model\Location\Location
     */
    public function getCurrentLocation()
    {
        if (!$this->currentLocation)
            $this->currentLocation = $this->registry->registry('current_location');
        if (!$this->currentLocation)
            $this->currentLocation = $this->registry->registry('current_warehouse');
        return $this->currentLocation;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $location = $this->getCurrentLocation();
        if (!$location || !$location->getId()) {
            return $meta;
        }
        $meta = array_replace_recursive(
            $meta,
            [
                $this->_groupContainer => [
                    'children' => $this->getChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->_groupLabel),
                                'autoRender' => true,
                                'collapsible' => true,
                                'visible' => $this->getVisible(),
                                'opened' => $this->getOpened(),
                                'componentType' => Form\Fieldset::NAME,
                                'sortOrder' => $this->_sortOrder
                            ],
                        ],
                    ],
                ],
            ]
        );
        return $meta;
    }

    /**
     * Retrieve child meta configuration
     *
     * @return array
     */
    public function getChildren()
    {
        $children = [
            'inventory_stock' => $this->getDashboardContainer(),
        ];
        return $children;
    }

    public function getDashboardContainer()
    {
        $listingTarget = 'webpos_inventory_stock_listing';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'autoRender' => true,
                        'componentType' => 'insertListing',
                        'dataScope' => $listingTarget,
                        'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                        'ns' => $listingTarget,
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                        'realTimeLink' => true,
                        'dataLinks' => [
                            'imports' => false,
                            'exports' => true
                        ],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [
                            'stockId' => '${ $.provider }:data.general_information.stock_selection.stock_id',
                        ],
                        'exports' => [
                            'stockId' => '${ $.externalProvider }:params.stock_id',
                        ]
                    ],
                ],
            ],
        ];
    }
}