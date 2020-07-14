<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Ui\DataProvider\Staff\Form\Modifier;

/**
 * Class AbstractModifier
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier
 */
class AbstractModifier extends \Magestore\Appadmin\Ui\DataProvider\Form\Modifier\AbstractModifier
{
    /**
     * @var \Magestore\Appadmin\Model\Staff\Staff
     */
    protected $currentStaff;

    /**
     * @var string
     */
    protected $scopeName = 'appadmin_staff_form.appadmin_staff_form';

    /**
     * @return \Magestore\Appadmin\Model\Staff\Staff|mixed
     */
    public function getCurrentStaff() {
        if (!$this->currentStaff)
            $this->currentStaff = $this->registry->registry('current_staff');
        return $this->currentStaff;
    }
}