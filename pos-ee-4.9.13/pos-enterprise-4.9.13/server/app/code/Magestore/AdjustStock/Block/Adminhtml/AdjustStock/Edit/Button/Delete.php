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
class Delete extends \Magestore\AdjustStock\Block\Adminhtml\AdjustStock\AbstractAdjustStock
    implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if($this->getRequest()->getParam('id') &&
            $this->_authorization->isAllowed('Magestore_AdjustStock::delete_adjuststock') &&
            $this->getAdjustStockStatus() != AdjustStockInterface::STATUS_COMPLETED) {
            $url = $this->getUrl('*/*/delete', array('_secure' => true, 'id' => $this->getRequest()->getParam('id')));

            return [
                'label' => __('Delete'),
                'on_click' => sprintf("deleteConfirm(
                        'Are you sure you want to delete this adjustment?', 
                        '%s'
                    )", $url),
                'class' => 'delete',
                'sort_order' => 20
            ];
        }
        return [];
    }

}
