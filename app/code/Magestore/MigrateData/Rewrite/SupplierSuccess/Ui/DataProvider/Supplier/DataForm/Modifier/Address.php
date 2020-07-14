<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\MigrateData\Rewrite\SupplierSuccess\Ui\DataProvider\Supplier\DataForm\Modifier;

use Magento\Ui\Component\Form\Field;

/**
 * Data provider for Configurable panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Address extends \Magestore\SupplierSuccess\Ui\DataProvider\Supplier\DataForm\Modifier\Address
{
    /**
     * Get supplier address children
     *
     * @return array
     */
    public function getSupplierAddressChildren()
    {
        $children = [
            'telephone' => $this->getField(__('Telephone'), Field::NAME, true, 'text', 'input'),
            'fax' => $this->getField(__('Fax'), Field::NAME, true, 'text', 'input'),
            'street' => $this->getField(__('Street'), Field::NAME, true, 'text', 'input'),
            'street_2' => $this->getField(__('Street 2'), Field::NAME, true, 'text', 'input'),
            'city' => $this->getField(__('City'), Field::NAME, true, 'text', 'input'),
            'country_id' => $this->getField(__('Country'), Field::NAME, true, 'text', 'select', [], null, $this->getCountries()),
            'region' => $this->getField(__('Region'), Field::NAME, false, 'text', 'input'),
            'region_id' => $this->getRegionIdField(),
            'postcode' => $this->getField(__('Zip/Postal Code'), Field::NAME, true, 'text', 'input'),
            'website' => $this->getField(__('Website'), Field::NAME, true, 'text', 'input')
        ];
        return $children;
    }
}
