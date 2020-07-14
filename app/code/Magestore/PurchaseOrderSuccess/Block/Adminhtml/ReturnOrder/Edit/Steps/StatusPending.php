<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Steps;

/**
 * Class StatusPending
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Steps
 */
class StatusPending extends \Magento\Ui\Block\Component\StepsWizard\StepAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return __('Pending');
    }

}