<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier;

use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;

/**
 * Class EmailToSupplier
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email
 */
class AbstractEmailToSupplier extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     */
    protected $purchaseOrder;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping
     */
    protected $taxShippingService;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Statusư
     */
    protected $status;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;
    
    protected $supplier;
    
    protected $currency;
    
    protected $taxType;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter,
        \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping $taxShippingService,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status $status,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->priceFormatter = $priceFormatter;
        $this->taxShippingService = $taxShippingService;
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->status = $status;
        $this->currencyFactory = $currencyFactory;
        $this->purchaseOrder = $this->getCurrentPurchaseOrder();
    }

    /**
     * Get current purchase order
     *
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     */
    public function getCurrentPurchaseOrder(){
        $purchaseOrder = $this->registry->registry('current_purchase_order');
        return $purchaseOrder;
    }
    
    public function getCurrentPurchaseOrderSupplier(){
        if(!$this->supplier)
            $this->supplier = $this->registry->registry('current_purchase_order_supplier');
        return $this->supplier;
    }

    /**
     * Get current purchase order
     *
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface
     */
    public function getCurrentReturnOrder(){
        $returnOrder = $this->registry->registry('current_return_order');
        return $returnOrder;
    }

    public function getCurrentReturnOrderSupplier(){
        $supplier = $this->registry->registry('current_return_order_supplier');
        return $supplier;
    }

    public function getCurrentReturnOrderWarehouse(){
        $warehouse = $this->registry->registry('current_return_order_warehouse');
        return $warehouse;
    }

    public function getReturnOrderData($field){
        return $this->getCurrentReturnOrder()->getData($field);
    }

    public function getReturnOrderItems(){
        return $this->getCurrentReturnOrder()->getItems();
    }

    public function getPriceFormat($price){
        if(!$this->currency)
            $this->currency = $this->currencyFactory->create()->load($this->purchaseOrder->getCurrencyCode());
        return $this->currency->formatTxt($price);
//        return $this->priceFormatter->convertAndFormat(
//            $price,
//            true,
//            null,
//            null,
//            $this->purchaseOrder->getCurrencyCode()
//        );
    }

    public function getPrice($code){
        return $this->purchaseOrder->getData($code);
    }

    public function getPurchaseOrderData($field){
        return $this->getCurrentPurchaseOrder()->getData($field);
    }

    public function getPurchaseOrderItems(){
        return $this->getCurrentPurchaseOrder()->getItems();
    }

    /**
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface $item
     */
    public function getItemTotal($item){
        $itemQty = $item->getQtyOrderred();
        $itemTotal = $itemQty * $item->getCost();
        $itemDiscount = $itemTotal*$item->getDiscount()/100;
        $taxType = $this->getTaxType();
        if($taxType == 0){
            $itemTax = $itemTotal*$item->getTax()/100;
        }else{
            $itemTax = ($itemTotal-$itemDiscount)*$item->getTax()/100;
        }
        return $this->getPriceFormat($itemTotal-$itemDiscount+$itemTax);
    }
    
    public function getTaxType(){
        if(!$this->taxType)
            $this->taxType = $this->taxShippingService->getTaxType();
        return $this->taxType;
    }
}