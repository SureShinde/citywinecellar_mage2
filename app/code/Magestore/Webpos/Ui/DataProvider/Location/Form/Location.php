<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\DataProvider\Location\Form;

use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory;
use Magestore\Webpos\Model\ResourceModel\Location\Location\Collection;

/**
 * Class Location
 * @package Magestore\Webpos\Ui\DataProvider\Location\Form
 */
class Location extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * @var array
     */
    protected $loadedData = [];

    /**
     * Location constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\Registry $registry
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Registry $registry,
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->_coreRegistry = $registry;
        $this->collection = $collectionFactory->create();
        $this->pool = $pool;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $location = $this->_coreRegistry->registry('current_location');
        if ($location && $location->getLocationId()) {
            $result = [
                'general_information' => [
                    'sub_general_information' => [
                        'location_id' => $location->getLocationId(),
                        'name' => $location->getName(),
                        'description' => $location->getDescription(),
                        'telephone' => $location->getTelephone(),
                        'email' => $location->getEmail()
                    ],
                    'stock_selection' => [
                        'stock_id' => $location->getStockId()
                    ],
                    'address_information' => [
                        'street' => $location->getStreet(),
                        'city' => $location->getCity(),
                        'country_id' => $location->getCountryId(),
                        'region' => $location->getRegion(),
                        'region_id' => $location->getRegionId(),
                        'postcode' => $location->getPostcode()
                    ]
                ],
                'custom_receipt' => [
                    'container_receipt_header' => [
                        'receipt_header' => $location->getReceiptHeader(),
                        'use_config_receipt_header' => $location->getUseConfigReceiptHeader()
                    ],
                    'container_receipt_footer' => [
                        'receipt_footer' => $location->getReceiptFooter(),
                        'use_config_receipt_footer' => $location->getUseConfigReceiptFooter()
                    ]
                ],
                'store_selection' => [
                    'store_id' => $location->getStoreId()
                ]
            ];
            $this->loadedData[$location->getLocationId()] = $result;
        }

        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->loadedData = $modifier->modifyData($this->loadedData);
        }

        return $this->loadedData;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        $meta = $meta = array_replace_recursive($meta, [
            'general_information' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'sortOrder' => 10
                        ]
                    ]
                ], 'children' => [
                    'sub_general_information' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'sortOrder' => 10
                                ]
                            ]
                        ],
                    ], 'address_information' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'sortOrder' => 30
                                ]
                            ]
                        ],
                    ]
                ]
            ]
        ]);

        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}