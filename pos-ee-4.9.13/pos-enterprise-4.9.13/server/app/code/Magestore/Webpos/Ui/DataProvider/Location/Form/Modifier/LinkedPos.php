<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\DataProvider\Location\Form\Modifier;

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Container;
use Magento\Framework\Phrase;
use Magento\Ui\Component\Form;

/**
 * Class LinkedPos
 * @package Magestore\Webpos\Ui\DataProvider\Location\Form\Modifier
 */
class LinkedPos extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Magestore\Webpos\Model\Location\Location
     */
    protected $currentLocation;

    protected $_groupLabel = 'Linked POS';
    protected $_sortOrder = 45;
    protected $_groupContainer = 'linked_pos';

    /**
     * General constructor.
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $_modifierConfig
     */
    public function __construct(
        \Magento\Framework\Registry $registry
    )
    {
        $this->registry = $registry;
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
            'linked_container' => $this->getDashboardContainer(),
        ];
        return $children;
    }

    public function getDashboardContainer()
    {
        $container = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'sortOrder' => 10,

                    ],
                ],
            ],
            'children' => [
                'html_content' => [
                    'arguments' => [
                        'data' => [
                            'type' => 'html_content',
                            'name' => 'html_content',
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/form/components/html',
                                'content' => \Magento\Framework\App\ObjectManager::getInstance()
                                    ->create('Magestore\Webpos\Block\Adminhtml\LinkedPos')
                                    ->toHtml()
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return $container;
    }
}