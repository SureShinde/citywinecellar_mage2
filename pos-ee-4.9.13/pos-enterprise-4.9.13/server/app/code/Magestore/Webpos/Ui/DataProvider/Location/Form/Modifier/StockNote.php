<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\DataProvider\Location\Form\Modifier;

/**
 * Class StockNote
 * @package Magestore\Webpos\Ui\DataProvider\Location\Form\Modifier
 */
class StockNote implements \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    /**
     * @var \Magestore\Webpos\Api\WebposManagementInterface
     */
    protected $webposManagement;

    /**
     * StockNote constructor.
     * @param \Magestore\Webpos\Api\WebposManagementInterface $webposManagement
     */
    public function __construct(
        \Magestore\Webpos\Api\WebposManagementInterface $webposManagement
    )
    {
        $this->webposManagement = $webposManagement;
    }

    /**
     * @param array $data
     * @return array
     * @since 100.1.0
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     * @since 100.1.0
     */
    public function modifyMeta(array $meta)
    {
        $remark = "You can only choose Stock that is linked to only one Source.";
        if ($this->webposManagement->isWebposStandard()) {
            $remark = "Only support Default Stock";
        }
        $meta = array_replace_recursive($meta, [
            'general_information' => [
                'children' => [
                    'stock_selection' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'sortOrder' => 20
                                ]
                            ]
                        ],
                        'children' => [
                            'stock_id' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'additionalInfo' => __(
                                                "Please select the corresponding Inventory Stock that will be associated with this location physically."
                                                . " </br>Remark: $remark"
                                            )
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'store_selection' => [
                'children' => [
                    'store_id' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'additionalInfo' => __(
                                        "Change this field for using multiple store view function."
                                        . " </br><a href='#'>Click here</a> to see more detail."
                                    )
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ], $meta);
        return $meta;
    }
}