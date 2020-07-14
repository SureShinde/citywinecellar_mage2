<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary;

use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;

/**
 * Class Total
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary
 */
class Total extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;
//
//    /**
//     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
//     */
//    protected $priceFormatter;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     */
    protected $purchaseOrder;

    /**
     * @var \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface
     */
    protected $supplierRepository;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository,
//        \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->purchaseOrderRepository = $purchaseOrderRepository;
//        $this->priceFormatter = $priceFormatter;
        $this->currencyFactory = $currencyFactory;
        $this->supplierRepository = $supplierRepository;
        $this->purchaseOrder = $this->getCurrentPurchaseOrder();
    }
    
    protected $_template = 'Magestore_PurchaseOrderSuccess::purchaseorder/form/purchasesumary/total.phtml';
    
    /**
     * Get current purchase order
     *
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     */
    public function getCurrentPurchaseOrder(){
        $purchaseOrder = $this->registry->registry('current_purchase_order');
        if(!$purchaseOrder || !$purchaseOrder->getId())
            $purchaseOrder = $this->purchaseOrderRepository->get($this->getRequest()->getParam('id'));
        return $purchaseOrder;
    }
    
    public function getPriceFormat($code){
        if(!$this->currency)
            $this->currency = $this->currencyFactory->create()->load($this->purchaseOrder->getCurrencyCode());
        return $this->currency->formatTxt($this->getPrice($code));
//        return $this->priceFormatter->convertAndFormat(
//            $this->getPrice($code),
//            true,
//            null,
//            null,
//            $this->purchaseOrder->getCurrencyCode()
//        );
    }
    
    public function getPrice($code){
        return $this->purchaseOrder->getData($code);
    }

    public function getSupplier(){
        $supplierId = $this->purchaseOrder->getSupplierId();
        try{
            $supplier = $this->supplierRepository->getById($supplierId);
        }catch (\Exception $exception){
            return '';
        }
        return $supplier->getSupplierName() . ' (' . $supplier->getSupplierCode() . ')';
    }
}