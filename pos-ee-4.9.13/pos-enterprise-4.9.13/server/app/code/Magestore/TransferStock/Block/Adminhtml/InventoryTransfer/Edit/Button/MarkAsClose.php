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
class MarkAsClose extends \Magestore\TransferStock\Block\Adminhtml\InventoryTransfer\AbstractInventoryTransfer
    implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if($this->request->getParam('id') && $this->getInventoryTransfer()->getStatus() == Status::STATUS_OPEN) {
            $url = $this->getUrl('*/*/markAsClose', array('_secure' => true, 'id' => $this->request->getParam('id')));

            return [
                'label' => __('Mark as Closed'),
                'on_click' => sprintf("deleteConfirm(
                        'Are you sure you want to mark this transfer as Closed?', 
                        '%s'
                    )", $url),
                'class' => 'delete',
                'sort_order' => 20
            ];
        }
        return [];
    }

}
