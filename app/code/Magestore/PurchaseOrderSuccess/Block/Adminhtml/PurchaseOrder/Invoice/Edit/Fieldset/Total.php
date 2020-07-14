<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Invoice\Edit\Fieldset;

/**
 * Class Total
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Invoice\Edit\Fieldset
 */
class Total extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface
     */
    protected $purchaseOrderInvoice;

    /**
     * @var string
     */
    protected $currencyCode;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\PurchaseOrderSuccess\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->invoiceRepository = $invoiceRepository;
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->priceFormatter = $priceFormatter;
        $this->currencyFactory = $currencyFactory;
        $this->purchaseOrderInvoice = $this->getCurrentPurchaseOrderInvoice();
    }
    
    protected $_template = 'Magestore_PurchaseOrderSuccess::purchaseorder/invoice/form/total.phtml';
    
    /**
     * Get current purchase order
     *
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     */
    public function getCurrentPurchaseOrderInvoice(){
        $purchaseOrder = $this->registry->registry('current_purchase_order_invoice');
        if(!$purchaseOrder || !$purchaseOrder->getId())
            $purchaseOrder = $this->purchaseOrderRepository->get($this->getRequest()->getParam('id'));
        return $purchaseOrder;
    }
    
    public function getPriceFormat($code){
        if(!$this->currency)
            $this->currency = $this->currencyFactory->create()->load($this->getCurrencyCode());
        return $this->currency->formatTxt($this->getPrice($code));
//        return $this->priceFormatter->convertAndFormat(
//            $this->getPrice($code),
//            true,
//            null,
//            null,
//            $this->getCurrencyCode()
//        );
    }
    
    public function getPrice($code){
        return $this->purchaseOrderInvoice->getData($code);
    }

    public function getCurrencyCode(){
        if(!$this->currencyCode){
            $this->currencyCode = $purchaseOrder = $this->purchaseOrderRepository
                ->get($this->purchaseOrderInvoice->getPurchaseOrderId())
                ->getCurrencyCode();
        }
        return $this->currencyCode;
    }
}