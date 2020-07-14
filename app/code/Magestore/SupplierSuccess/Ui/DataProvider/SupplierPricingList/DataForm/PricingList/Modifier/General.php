<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Ui\DataProvider\SupplierPricingList\DataForm\PricingList\Modifier;

use Magestore\SupplierSuccess\Ui\DataProvider\Supplier\DataForm\Modifier\AbstractModifier;
/**
 * Class General
 * @package Magestore\SupplierSuccess\Ui\DataProvider\SupplierPricingList\DataForm\PricingList\Modifier
 */
class General extends AbstractModifier
{
    /**
     * modify data
     *
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Modify purchase order form meta
     * 
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta){
        $meta = array_replace_recursive(
            $meta,
            [
                'general_information' => [
                    'children' => $this->getGeneralChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Select a supplier'),
                                'collapsible' => true,
                                'dataScope' => 'data',
                                'visible' => true,
                                'opened' => true,
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => 10
                            ],
                        ],
                    ],
                ],
            ]
        );
        return $meta;   
    }

    /**
     * Add general form fields
     * 
     * @return array
     */
    public function getGeneralChildren(){
        $children = [
            'supplier_id' => $this->getField(__('Supplier'), \Magento\Ui\Component\Form\Field::NAME, true, 'text', 'select', ['required-entry' => true], null, $this->getSuppliers()),
        ];
//        $children = [
//            'supplier_id' => [
//                'arguments' => [
//                    'data' => [
//                        'config' => [
//                            'dataType' => 'text',
//                            'formElement' => 'select',
//                            'component' => 'Magestore_PurchaseOrderSuccess/js/form/element/select',
//                            'options' =>  $this->getSuppliers(),
//                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
//                            'label' => __('Supplier'),
//                            'sortOrder' => 10,
//                            'reloadObjectListing' =>
////                                'os_supplier_pricinglist_form.os_supplier_pricinglist_form.pricing_list.pricing_list_product_select_modal.pricing_list_product_modal_select_listing',
//                                'os_supplier_pricinglist_modal_add_listing.os_supplier_pricinglist_modal_add_listing.pricing_list.pricing_list_product_select_modal.pricing_list_product_modal_select_listing',
//                            'reloadParam' => 'supplier_id'
//                        ],
//                    ],
//                ],
//            ],
//        ];
//        \Zend_Debug::dump($children);
        return $children;
    }

    /**
     * Retrieve countries
     *
     * @return array|null
     */
    public function getSuppliers()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create(
            'Magestore\SupplierSuccess\Model\Source\PricingList\Supplier'
        )->toOptionArray();
    }
}