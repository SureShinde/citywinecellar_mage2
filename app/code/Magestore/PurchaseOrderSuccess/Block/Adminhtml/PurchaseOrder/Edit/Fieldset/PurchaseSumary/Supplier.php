<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary;

/**
 * Class Supplier
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary
 */
class Supplier extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     */
    protected $purchaseOrder;

    /**
     * @var \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface
     */
    protected $supplierRepository;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->supplierRepository = $supplierRepository;
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->purchaseOrder = $this->getCurrentPurchaseOrder();
    }
    
    protected $_template = 'Magestore_PurchaseOrderSuccess::purchaseorder/form/purchasesumary/supplier.phtml';
    
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

    public function getSupplierInformation(){
        $supplierId = $this->purchaseOrder->getSupplierId();
        try{
            $supplier = $this->supplierRepository->getById($supplierId);
            $this->setData('current_supplier',$supplier);
        }catch (\Exception $exception){
            return '';
        }
        $html = $supplier->getSupplierName() . ' (' . $supplier->getSupplierCode() . ')';
        $html.= '<br/>'. $this->getFormatedAddress();
        return $html;
    }

    public function getSupplierData($field){
        return $this->getData('current_supplier')->getData($field);
    }
    
    /**
     * Get formatted address
     * 
     * @return string
     */
    public function getFormatedAddress()
    {
        $address = '';
        $region = $this->getRegion();    
        $postCode = $this->getSupplierData('postcode');
        $city = $this->getSupplierData('city');
        $cityRegionZip = [];        
        
        if($city) {
            $cityRegionZip[] = $city;
        }
        if($region) {
            $cityRegionZip[] = $region;
        }
        if($postCode) {
            $cityRegionZip[] = $postCode;
        }
        $address .= $this->getSupplierData('street') . '<br/>';
        $address .= implode(', ', $cityRegionZip) . '<br/>';
        $address .= $this->getCountry();
        
        return $address;
    }

    public function getStreetCity(){
        $result = [];
        $street = $this->getSupplierData('street');
        $city = $this->getSupplierData('city');
        if($street)
            $result[] = $street;
        if($city)
            $result[] = $city;
        if(!empty($result))
            return '<br/>'.implode(', ', $result);
        return '';
    }

    /**
     * @return string
     */
    public function getPostCodeRegionCountry(){
        $result = [];
        $postCode = $this->getSupplierData('postcode');
        $region = $this->getRegion();
        $country = $this->getCountry();
        if($postCode)
            $result[] = $postCode;
        if($region)
            $result[] = $region;
        if($country)
            $result[] = $country;
        if(!empty($result))
            return '<br/>'.implode(', ', $result);
        return '';
    }

    /**
     * @return string
     */
    public function getCountry(){
        if($this->getSupplierData('country_id'))
            return $this->countryFactory->create()->loadByCode(
                $this->getSupplierData('country_id')
            )->getName();
        return '';
    }

    /**
     * @return string
     */
    public function getRegion(){
        if($this->getSupplierData('region_id'))
            return $this->regionFactory->create()->load($this->getSupplierData('region_id'))->getName();
        return $this->getSupplierData('region');
    }
    
    /**
     * Get purchase date of PO
     * 
     * @return string
     */
    public function getPurchaseDate()
    {
        $timezone = new \DateTimeZone($this->_localeDate->getConfigTimezone());
        $createdDate = \DateTime::createFromFormat('Y-m-d', $this->getCurrentPurchaseOrder()->getPurchasedAt(), $timezone);
        return $this->formatDate($createdDate, \IntlDateFormatter::LONG);
    }
    
    /**
     * 
     * @return string
     */
    public function getPOPaymentTerm()
    {
        $paymentTerm = $this->purchaseOrder->getPaymentTerm();
        if($paymentTerm &&
            $paymentTerm!=\Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\PaymentTerm::OPTION_NONE_VALUE
        )
            return $paymentTerm;
        return __('N/A');
    }
    
    
    /**
     * 
     * @return string
     */
    public function getPOShippingMethod()
    {
        $shippingMethod = $this->purchaseOrder->getShippingMethod();
        if($shippingMethod &&
            $shippingMethod!=\Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\ShippingMethod::OPTION_NONE_VALUE
        )
            return $shippingMethod;
        return __('N/A');
    }    
}