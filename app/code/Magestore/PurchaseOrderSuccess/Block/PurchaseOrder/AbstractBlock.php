<?php

namespace Magestore\PurchaseOrderSuccess\Block\PurchaseOrder;

use Magento\Framework\View\Element\Template;
use Magestore\SupplierSuccess\Model\SupplierFactory;

abstract class AbstractBlock extends \Magento\Framework\View\Element\Template {

    protected $_purchaseOrder;
    protected $_purchaseOrderSupplier;
    protected $currency;
    protected $taxType;

    protected $request;

    protected $purchaseOrderRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping
     */
    protected $taxShippingService;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceFormatter;

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

    protected $supplierFactory;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        Template\Context $context, array $data = [],
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter,
        \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping $taxShippingService,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status $status,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magestore\SupplierSuccess\Model\SupplierFactory $supplierFactory,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository
    ) {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->priceFormatter = $priceFormatter;
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->taxShippingService = $taxShippingService;
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->status = $status;
        $this->currencyFactory = $currencyFactory;
        $this->supplierFactory = $supplierFactory;
    }

    public function getPurchaseOrderItems() {
        return $this->getCurrentPurchaseOrder()->getItems();
    }

    /**
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     */
    public function getCurrentPurchaseOrder() {
        if(!$this->_purchaseOrder) {
            $keyPo = $this->request->getParam('key');
            $this->_purchaseOrder = $this->purchaseOrderRepository->getByKey($keyPo);
        }
        return $this->_purchaseOrder;
    }

    public function getCurrentPurchaseOrderSupplier(){
        if(!$this->_purchaseOrderSupplier) {
            $purchaseOrder = $this->getCurrentPurchaseOrder();
            $this->_purchaseOrderSupplier = $this->supplierFactory->create()
                ->load($purchaseOrder->getSupplierId());
        }
        return $this->_purchaseOrderSupplier;
    }

    public function getPriceFormat($price){
        if(!$this->currency)
            $this->currency = $this->currencyFactory->create()->load($this->_purchaseOrder->getCurrencyCode());
        return $this->currency->formatTxt($price);
    }

    public function getPurchaseOrderData($field){
        return $this->getCurrentPurchaseOrder()->getData($field);
    }

    public function getPrice($code){
        return $this->getCurrentPurchaseOrder()->getData($code);
    }

    public function getSupplierData($field){
        return $this->getCurrentPurchaseOrderSupplier()->getData($field);
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