<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier;

/**
 * Class General
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier
 */
class General extends AbstractModifier
{
    /**
     * @var string
     */
    protected $groupContainer = 'general_information';

    /**
     * @var string
     */
    protected $groupLabel = 'General Information';

    /**
     * @var int
     */
    protected $sortOrder = 80;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->localeDate = $localeDate;
        parent::__construct($objectManager, $registry, $request, $urlBuilder);
    }

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
                $this->groupContainer => [
                    'children' => $this->getGeneralChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->groupLabel),
                                'collapsible' => true,
                                'dataScope' => 'data',
                                'visible' => $this->getVisible(),
                                'opened' => $this->getOpened(),
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => $this->getSortOrder()
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
        $disable = !$this->getOpened();
        $type = $this->request->getParam('type', '2');
        $suppliers = $this->objectManager->create('Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\SupplierEnable');
        $localeList = $this->objectManager->create('Magento\Framework\Locale\ListsInterface');
        $baseCurrencyCode = $this->objectManager->create('Magento\Directory\Helper\Data')->getBaseCurrencyCode();
        $defaultCurrencyRate = $disable?null:1;
        $children = [
            'purchase_order_id' => $this->addFormFieldText('', 'hidden', 10),
            'type' => $this->addFormFieldText('Type', 'hidden', 20, false, $type),
            'purchased_at' => $this->addFormFieldDate('Created Time', 30, true, $this->localeDate->date()->format('Y-m-d')),
            'supplier_id' => $this->addFormFieldSelect(
                'Supplier', $suppliers->getOptionArray(), 40, true, null, '', null, $disable
            ),
            'currency_code' => $this->addFormFieldSelect(
                'Currency', $localeList->getOptionCurrencies(), 50, true, $baseCurrencyCode, '', null, $disable
            ),
            'currency_rate' => $this->addFormFieldText(
                'Currency Exchange Rate', 'input', 51, true, $defaultCurrencyRate, 'notice', $disable
            ),
            'currency_extra' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement' => 'container',
                            'componentType' => 'container',
                            'component' => 'Magestore_PurchaseOrderSuccess/js/form/element/currency-extra',
                            'provider' => null,
                            'title' => __(''),
                            'visible' => false,
                            'baseCurrencyCode' => $baseCurrencyCode,
                        ],
                    ],
                ],
            ],
            //'send_email' => $this->addFormFieldCheckbox('Send Email to Supplier', 50),
            'comment' => $this->addFormFieldTextArea('Comment', 60)
        ];
        $children['currency_rate']['arguments']['data']['config']['validation'] = [
            'required-entry' => true,
            'validate-number' => true,
            'validate-greater-than-zero' => true,
        ];
        return $children;
    }

    public function getOpened(){
        if(!$this->request->getParam('id')){
            return true;
        }
        return $this->opened;
    }
}
