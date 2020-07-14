<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\TransferStock\Block\Adminhtml\InventoryTransfer\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magestore\TransferStock\Model\InventoryTransfer\Option\Stage;
use Magestore\TransferStock\Model\InventoryTransfer\Option\Status;

/**
 * Class SaveButton
 */
class Update extends \Magestore\TransferStock\Block\Adminhtml\InventoryTransfer\AbstractInventoryTransfer
    implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        $inventoryTransfer = $this->getInventoryTransfer();
        if ($this->request->getParam('id')
            && $inventoryTransfer->getStage() == Stage::STAGE_NEW
            && $inventoryTransfer->getStatus() == Status::STATUS_OPEN) {
            $data = [
                'label' => __('Save'),
                'class' => 'save primary',
                'on_click' => '',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'os_inventorytransfer_edit_form.areas',
                                    'actionName' => 'save',
                                    'params' => [
                                        true
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
                'sort_order' => 90,
            ];
        }

        return $data;
    }
}
