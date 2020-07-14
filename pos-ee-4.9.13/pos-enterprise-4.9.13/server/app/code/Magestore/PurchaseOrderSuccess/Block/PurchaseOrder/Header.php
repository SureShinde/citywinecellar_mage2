<?php

namespace Magestore\PurchaseOrderSuccess\Block\PurchaseOrder;

class Header extends \Magestore\PurchaseOrderSuccess\Block\PurchaseOrder\AbstractBlock {
    protected $_purchaseOrder;
    protected $_purchaseOrderSupplier;
    protected $currency;
    protected $taxType;

    public function getDataHtml($field){
        $value = $this->getSupplierData($field);
        if($value)
            return "<span>".$value."</span><br/>";
        return '';
    }

    /**
     * @return string
     */
    public function getCityRegionPostCode(){
        $result = [];
        $city = $this->getSupplierData('city');
        $region = $this->getRegion();
        $postCode = $this->getSupplierData('postcode');
        if($city)
            $result[] = $city;
        if($region)
            $result[] = $region;
        if($postCode)
            $result[] = $postCode;
        if(!empty($result))
            return "<span>".implode(', ', $result)."</span><br/>";
        return '';
    }

    /**
     * @return string
     */
    public function getCountry(){
        if($this->getSupplierData('country_id'))
            return "<span>".$this->countryFactory->create()->loadByCode(
                    $this->getSupplierData('country_id')
                )->getName()."</span><br/>";
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

    public function getPurchaseOrderStatus(){
        $status = $this->getPurchaseOrderData('status');
        $options = $this->status->getOptionHash();
        return $options[$status];
    }

    /**
     *
     * @return string
     */
    public function getPurchaseOrderCode()
    {
        return $this->getCurrentPurchaseOrder()->getPurchaseCode();
    }

    /**
     * Get purchase date of PO
     *
     * @return string
     */
    public function getPurchaseDate()
    {
        return $this->formatDate(
            $this->getCurrentPurchaseOrder()->getPurchasedAt(),
            \IntlDateFormatter::LONG
        );
    }
}