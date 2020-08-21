<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\TransferStock\Block\Adminhtml\InventoryTransfer\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Back button
 */
class Back extends \Magestore\TransferStock\Block\Adminhtml\InventoryTransfer\AbstractInventoryTransfer implements
    ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf(
                "location.href = '%s';",
                $this->getUrl('*/*/', ['_secure' => true])
            ),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}
