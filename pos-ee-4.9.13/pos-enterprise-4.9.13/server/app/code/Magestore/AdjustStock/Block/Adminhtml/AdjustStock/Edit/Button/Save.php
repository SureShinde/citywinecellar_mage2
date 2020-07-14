<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\AdjustStock\Block\Adminhtml\AdjustStock\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Ui\Component\Control\Container;
use Magestore\AdjustStock\Api\Data\AdjustStock\AdjustStockInterface;

/**
 * Class SaveButton
 */
class Save extends \Magestore\AdjustStock\Block\Adminhtml\AdjustStock\AbstractAdjustStock
    implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if (!$this->getRequest()->getParam('id')) {
            return [
                'label' => __('Start to Adjust Stock'),
                'class' => 'save primary',
                'on_click' => '',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'os_adjuststock_form.os_adjuststock_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        [
                                            'back' => 'edit'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'form-role' => 'save',
                ],
                'sort_order' => 40,
            ];
        }
        if ($this->getAdjustStockStatus() == AdjustStockInterface::STATUS_COMPLETED) {
            return [
                'label' => __('Export Products'),
                'class' => 'save primary',
                'on_click' => sprintf(
                    "location.href = '%s';",
                    $this->getUrl(
                        '*/*/export',
                        ['_secure' => true, 'id' => $this->getRequest()->getParam('id')]
                    )
                ),
                'sort_order' => 40
            ];
        }
        if ($this->getAdjustStockStatus() != AdjustStockInterface::STATUS_COMPLETED) {
            return [
                'label' => __('Save'),
                'class' => 'save primary',
                'on_click' => '',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'os_adjuststock_form.os_adjuststock_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        false
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'class_name' => Container::SPLIT_BUTTON,
                'options' => $this->getOptions(),
            ];
        }
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    public function getOptions()
    {
        $options[] = [
            'id_hard' => 'save_and_new',
            'label' => __('Save & New'),
            'on_click' => '',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'os_adjuststock_form.os_adjuststock_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => 'new'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        $options[] = [
            'id_hard' => 'save_and_close',
            'label' => __('Save & Close'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'os_adjuststock_form.os_adjuststock_form',
                                'actionName' => 'save',
                                'params' => [
                                    true
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        return $options;
    }
}
