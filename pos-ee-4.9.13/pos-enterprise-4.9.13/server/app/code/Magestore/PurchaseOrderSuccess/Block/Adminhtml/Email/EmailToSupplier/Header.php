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
class Header extends AbstractEmailToSupplier
{
    protected $_template = 'Magestore_PurchaseOrderSuccess::email/email_to_supplier/header.phtml';

    public function getSupplierData($field){
        return $this->getCurrentPurchaseOrderSupplier()->getData($field);
    }

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
        $localeDate = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\Framework\Stdlib\DateTime\TimezoneInterface'
        );
        $dateTime = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\Framework\Stdlib\DateTime\DateTime'
        );
        $date = new \DateTime($this->getCurrentPurchaseOrder()->getPurchasedAt());
        $date = $localeDate->date($date->getTimeStamp() - $dateTime->getGmtOffset());
        return $this->formatDate($date, \IntlDateFormatter::LONG);
    }
}
