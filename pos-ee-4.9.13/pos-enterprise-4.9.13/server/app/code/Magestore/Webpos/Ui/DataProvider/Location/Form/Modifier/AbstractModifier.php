<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\DataProvider\Location\Form\Modifier;

/**
 * Class AbstractModifier
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier
 */
class AbstractModifier extends \Magestore\Webpos\Ui\DataProvider\Form\Modifier\AbstractModifier
{
    /**
     * @var \Magestore\Webpos\Model\Location\Location
     */
    protected $currentLocation;

    /**
     * @var string
     */
    protected $scopeName = 'webpos_location_form.webpos_location_form';

    /**
     * @return \Magestore\Webpos\Model\Location\Location|mixed
     */
    public function getCurrentLocation() {
        if (!$this->currentLocation)
            $this->currentLocation = $this->registry->registry('current_location');
        return $this->currentLocation;
    }
}