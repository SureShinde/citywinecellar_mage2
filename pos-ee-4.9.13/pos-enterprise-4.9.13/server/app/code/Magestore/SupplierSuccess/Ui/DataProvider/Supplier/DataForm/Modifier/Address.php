<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Ui\DataProvider\Supplier\DataForm\Modifier;

use Magento\Ui\Component\Form;
use Magento\Ui\Component\Form\Field;
use Magento\Directory\Model\Config\Source\Country as SourceCountry;
use Magento\Directory\Helper\Data as DirectoryHelper;

/**
 * Data provider for Configurable panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Address extends AbstractModifier
{
    /**
     * @var array
     */
    protected $countries;

    /**
     * @var SourceCountry
     */
    protected $sourceCountry;

    /**
     * @var array
     */
    protected $regions;

    public function __construct(
        SourceCountry $sourceCountry,
        DirectoryHelper $directoryHelper
    ) {
        $this->sourceCountry = $sourceCountry;
        $this->directoryHelper = $directoryHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            $this->getSupplierInformation($meta)
        );
        return $meta;
    }

    /**
     * @param $meta
     * @return mixed
     */
    public function getSupplierInformation($meta)
    {
        $meta['address']['arguments']['data']['config'] = [
            'label' => __('Supplier Address'),
            'collapsible' => true,
            'visible' => true,
            'opened' => false,
            'dataScope' => 'data',
            'componentType' => Form\Fieldset::NAME
        ];
        $meta['address']['children'] = $this->getSupplierAddressChildren();
        return $meta;
    }

    /**
     * @return array
     */
    public function getSupplierAddressChildren()
    {
        $children = [
            'telephone' => $this->getField(__('Telephone'), Field::NAME, true, 'text', 'input'),
            'fax' => $this->getField(__('Fax'), Field::NAME, true, 'text', 'input'),
            'street' => $this->getField(__('Street'), Field::NAME, true, 'text', 'input'),
            'city' => $this->getField(__('City'), Field::NAME, true, 'text', 'input'),
            'country_id' => $this->getField(__('Country'), Field::NAME, true, 'text', 'select', [], null, $this->getCountries()),
            'region' => $this->getField(__('Region'), Field::NAME, false, 'text', 'input'),
            'region_id' => $this->getRegionIdField(),
            'postcode' => $this->getField(__('Zip/Postal Code'), Field::NAME, true, 'text', 'input'),
            'website' => $this->getField(__('Website'), Field::NAME, true, 'text', 'input')
        ];
        return $children;
    }

    /**
     * Retrieve countries
     *
     * @return array|null
     */
    public function getCountries()
    {
        if (null === $this->countries) {
            $this->countries = $this->sourceCountry->toOptionArray();
        }

        return $this->countries;
    }


    /**
     * @return array
     */
    public function getRegionIdField()
    {
        $field = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'select',
                        'component' => 'Magestore_SupplierSuccess/js/form/element/region',
//                        'elementTmpl' => 'ui/form/element/select',
                        'customEntry' => 'region',
                        'componentType' => Field::NAME,
                        'label' => __('Region'),
                        'dataType' => 'text',
                        'options' => $this->getRegions(),
                        'dataScope' => 'region_id',
                        'sortOrder' => 90,
                        'filterBy' => [
                            'target' => 'os_supplier_form.os_supplier_form.address.country_id',
                            'field' => 'country',
                        ],
                    ],
                ],
            ],
        ];
        return $field;
    }

    /**
     * Retrieve regions
     *
     * @return array
     */
    public function getRegions()
    {
        if (null === $this->regions) {
            $regions = $this->directoryHelper->getRegionData();
            $this->regions = [];

            unset($regions['config']);

            foreach ($regions as $countryCode => $countryRegions) {
                foreach ($countryRegions as $regionId => $regionData) {
                    $this->regions[] = [
                        'label' => $regionData['name'],
                        'value' => $regionId,
                        'country' => $countryCode,
                    ];
                }
            }
        }

        return $this->regions;

    }
}
