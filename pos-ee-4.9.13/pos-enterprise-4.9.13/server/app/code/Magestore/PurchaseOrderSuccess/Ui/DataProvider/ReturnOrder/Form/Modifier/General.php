<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\Form\Modifier;

/**
 * Class General
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\Form\Modifier
 */
class General extends AbstractModifier
{

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
        $defaultDate = new \DateTime();
        $disable = !$this->getOpened();
        $suppliers = $this->objectManager->create('Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\SupplierEnable');

        $warehouses = $this->objectManager->create('Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\WarehouseEnable');
        $children = [
            'return_id' => $this->addFormFieldText('', 'hidden', 10),
            'returned_at' => $this->addFormFieldDate('Created Time', 30, true, $this->localeDate->date()->format('Y-m-d')),
            'warehouse_id' => $this->addFormFieldSelect(
                'Source', $warehouses->getOptionArray(), 35, true, null, '', null, $disable
            ),
            'supplier_id' => $this->addFormFieldSelect(
                'Supplier', $suppliers->getOptionArray(), 40, true, null, '', null, $disable
            ),
            'reason' => $this->addFormFieldTextArea('Reason', 60)
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