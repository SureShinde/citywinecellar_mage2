<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Ui\DataProvider\Supplier\DataForm\Modifier;

use Magento\Ui\Component\Form;
use Magento\Ui\Component\Form\Field;

/**
 * Data provider for Configurable panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Password extends AbstractModifier
{
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
            $this->getSupplierPassword($meta)
        );
        return $meta;
    }

    /**
     * @param $meta
     * @return mixed
     */
    public function getSupplierPassword($meta)
    {
        if ($this->moduleManager->isEnabled('Magestore_DropshipSuccess')) {
            $meta['password']['arguments']['data']['config'] = [
                'label' => __('Password Management (use to login Drop Shipping Management)'),
                'collapsible' => true,
                'visible' => true,
                'opened' => false,
                'dataScope' => 'data',
                'componentType' => Form\Fieldset::NAME
            ];
            $meta['password']['children'] = $this->getSupplierPasswordChildren();
        }
        return $meta;
    }

    /**
     * @return array
     */
    public function getSupplierPasswordChildren()
    {
        $children = [
            'new_password' => $this->getField(__('New Password'), Field::NAME, true, 'new_password', 'input', []),
//            'generated_password' => $this->getField(__('Auto-generated password'), Field::NAME, true, '', 'checkbox'),
            'generated_password' => $this->getGeneralPassWord(),
            'send_pass_to_supplier' => $this->sendPassToSupplier(),
        ];
        return $children;
    }

    /**
     * @return array
     */
    public function getGeneralPassWord()
    {
        $container = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataType' => 'boolean',
                        'label' => __('Auto-generated password'),
                        'formElement' => 'checkbox',
                        'componentType' => 'field',
                        'prefer' => 'toggle',
                        'valueMap' => [
                            'true' => 1,
                            'false' => 0
                        ],
                        'default' => 0,
                    ]
                ]
            ]
        ];
        return $container;
    }

    /**
     * @return array
     */
    public function sendPassToSupplier()
    {
        $container = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataType' => 'boolean',
                        'label' => __('Send new password to the supplier'),
                        'formElement' => 'checkbox',
                        'componentType' => 'field',
                        'prefer' => 'toggle',
                        'valueMap' => [
                            'true' => 1,
                            'false' => 0
                        ],
                        'default' => 0,
                    ]
                ]
            ]
        ];
        return $container;
    }
}
