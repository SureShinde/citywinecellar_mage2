<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\AdjustStock\Block\Adminhtml\AdjustStock\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magestore\AdjustStock\Api\Data\AdjustStock\AdjustStockInterface;

/**
 * Class SaveButton
 */
class Adjust extends \Magestore\AdjustStock\Block\Adminhtml\AdjustStock\AbstractAdjustStock
    implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if($this->_authorization
                ->isAllowed('Magestore_AdjustStock::confirm_adjuststock')
            && $this->getAdjustStockStatus() != AdjustStockInterface::STATUS_COMPLETED
            && $this->getRequest()->getParam('id')) {
            return [
                'label' => __('Save and Apply'),
                'class' => 'save',
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
                                            'back' => 'apply'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort_order' => 30
            ];
        }
        return;
    }

}
