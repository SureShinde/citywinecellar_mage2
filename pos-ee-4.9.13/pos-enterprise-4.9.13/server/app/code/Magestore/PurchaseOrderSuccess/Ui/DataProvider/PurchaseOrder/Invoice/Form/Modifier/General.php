<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Invoice\Form\Modifier;

/**
 * Class General
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Invoice\Form\Modifier
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
    protected $groupLabel = 'Billed From';

    /**
     * @var int
     */
    protected $sortOrder = 10;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepositoryInterface;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var
     */
    protected $timezone;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepositoryInterface,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        parent::__construct($objectManager, $registry, $request, $urlBuilder);
        $this->purchaseOrderRepositoryInterface = $purchaseOrderRepositoryInterface;
        $this->dateTime = $dateTime;
        $this->timezone = $timezone;
    }

    /**
     * modify data
     *
     * @return array
     */
    public function modifyData(array $data)
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->objectManager->get(
            '\Magento\Framework\App\Request\Http'
        );
//        var_dump(strtolower($request->getControllerModule()));
        if (strtolower($request->getControllerName()) == 'purchaseorder_invoice') {
            $id = $request->getParam('id');
            $purchaseOrderId = $data[$id]['purchase_order_id'];
            /** @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository */
            $purchaseOrderRepository = $this->objectManager->get(
                '\Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface'
            );
            $purchaseOrder = $purchaseOrderRepository->get($purchaseOrderId);
            $supplierId = $purchaseOrder->getSupplierId();
        } else {
            $id = $this->getPurchaseOrderId();
            $supplierId = $data[$id]['supplier_id'];
        }
        if (isset($supplierId)) {
            /** @var \Magestore\SupplierSuccess\Service\SupplierService $supplierService */
            $supplierService = $this->objectManager->get(
                '\Magestore\SupplierSuccess\Service\SupplierService'
            );
            $supplierInformation = $supplierService->getSupplierInformationHtml($supplierId);
            $data[$id]['billed_from'] = $supplierInformation;
        }
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
                                'opened' => true,
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
        $currentPurchaseOrder = $this->getCurrentPurchaseOrder();
        $purchaseOrderDate = $this->timezone->date($currentPurchaseOrder->getPurchasedAt(), null, false);
        $children = [
            'purchase_order_id' => $this->addFormFieldText('', 'hidden', 10),
            'billed_from' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'label' => __('Billed From'),
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'sortOrder' => 10,
                            'dataScope' => 'billed_from',
                            'elementTmpl' => 'Magestore_PurchaseOrderSuccess/form/element/text'
                        ],
                    ],
                ],
            ],
            'billed_at' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'label' => __('Billed Date'),
                            'dataType' => 'date',
                            'formElement' => 'date',
                            'sortOrder' => 20,
                            'dataScope' => 'billed_at',
                            'validation' => ['required-entry' => true],
                            'default' => $this->timezone->date()->format('Y-m-d'),
                            'options' => [
                                'minDate' => $currentPurchaseOrder && $currentPurchaseOrder->getPurchaseOrderId() ?
                                    $this->timezone->formatDate($purchaseOrderDate): null
                            ]
                        ],
                    ],
                ],
            ]
        ];
        return $children;
    }

    public function getCurrentPurchaseOrder(){
        $currentPurchaseOrder = $this->registry->registry('current_purchase_order');
        if(!$currentPurchaseOrder || !$currentPurchaseOrder->getPurchaseOrderId()){
            $purchaseId = $this->request->getParam('purchase_id');
            $currentPurchaseOrder = $this->purchaseOrderRepositoryInterface->get($purchaseId);
            $this->registry->register('current_purchase_order', $currentPurchaseOrder);
        }
        return $currentPurchaseOrder;
    }
}