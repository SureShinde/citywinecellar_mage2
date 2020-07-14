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
class HeaderEmail extends AbstractEmailToSupplier
{
    protected $_template = 'Magestore_PurchaseOrderSuccess::email/email_to_supplier/header.phtml';

    /**
     * @return string
     */
    public function getTemplate()
    {
        $type = $this->getPurchaseOrderData('type');
        if($type == \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type::TYPE_PURCHASE_ORDER) {
            return 'Magestore_PurchaseOrderSuccess::email/email_to_supplier/headerPurchaseOrder.phtml';
        }
        return $this->_template;
    }

    public function getDownloadCsvLink() {
        $purchaseKey = $this->getPurchaseOrderData('purchase_key');
        $urlBuilder = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\Url');
        return $urlBuilder->getUrl('purchaseordersuccess/purchaseOrder/downloadcsv', ['key' => $purchaseKey]);
    }

    public function getDownloadPdfLink() {
        $purchaseKey = $this->getPurchaseOrderData('purchase_key');
        $urlBuilder = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\Url');
        return $urlBuilder->getUrl('purchaseordersuccess/purchaseOrder/downloadpdf', ['key' => $purchaseKey]);
    }

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
        $date = $localeDate->date($this->getCurrentPurchaseOrder()->getPurchasedAt());
        $date = $localeDate->date($date->getTimeStamp() - $dateTime->getGmtOffset());
        return $this->formatDate($date, \IntlDateFormatter::LONG);
    }
}
