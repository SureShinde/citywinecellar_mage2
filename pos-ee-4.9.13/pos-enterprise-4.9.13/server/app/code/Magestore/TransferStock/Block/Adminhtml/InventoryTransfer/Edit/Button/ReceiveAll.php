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
class ReceiveAll extends \Magestore\TransferStock\Block\Adminhtml\InventoryTransfer\AbstractInventoryTransfer
    implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $inventoryTransfer = $this->getInventoryTransfer();
        if ($this->request->getParam('id')
            && (
                $inventoryTransfer->getStage() == Stage::STAGE_SENT
                || $inventoryTransfer->getStage() == Stage::STAGE_RECEIVING
            )
            && $inventoryTransfer->getStatus() == Status::STATUS_OPEN) {
            $url = $this->getUrl('*/*/receiveAll', array('_secure' => true, 'id' => $this->request->getParam('id')));

            return [
                'label' => __('Receive All'),
                'on_click' => sprintf("deleteConfirm(
                        'Are you sure you want to receive all products?', 
                        '%s'
                    )", $url),
                'class' => 'delete',
                'sort_order' => 30
            ];
        }
        return [];
    }

}
