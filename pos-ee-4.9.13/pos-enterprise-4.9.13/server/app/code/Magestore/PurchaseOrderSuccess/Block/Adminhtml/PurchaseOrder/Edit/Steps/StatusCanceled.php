<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Steps;

/**
 * Class StatusCanceled
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Steps
 */
class StatusCanceled extends  \Magento\Ui\Block\Component\StepsWizard\StepAbstract
{
    public function getCaption()
    {
        return __('Canceled');
    }

}