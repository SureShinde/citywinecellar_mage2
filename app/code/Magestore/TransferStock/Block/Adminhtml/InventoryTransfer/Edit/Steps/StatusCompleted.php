<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Block\Adminhtml\InventoryTransfer\Edit\Steps;
/**
 * Class StatusCompleted
 * @package Magestore\TransferStock\Block\Adminhtml\InventoryTransfer\Edit\Steps
 */
class StatusCompleted extends \Magento\Ui\Block\Component\StepsWizard\StepAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return __('Completed');
    }

}