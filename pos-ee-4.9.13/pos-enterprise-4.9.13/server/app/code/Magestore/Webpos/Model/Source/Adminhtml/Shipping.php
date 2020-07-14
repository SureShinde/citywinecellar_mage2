<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * Class Shipping
 * @package Magestore\Webpos\Model\Source\Adminhtml
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Shipping implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *
     */
    const SPECIFIC_SHIPPING = 'webpos/shipping/method';

    /**
     * @var array
     */
    protected $allowShippings = array();

    /**
     * @var \Magestore\Webpos\Api\Data\Shipping\ShippingMethodInterfaceFactory
     */
    protected $shippingMethodFactory;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shippingConfigModel;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;


    /**
     * Shipping constructor.
     * @param \Magestore\Webpos\Api\Data\Shipping\ShippingMethodInterfaceFactory $shippingModel
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config $shippingConfigModel
     * @param \Magestore\Webpos\Helper\Data $helper
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\Shipping\ShippingMethodInterfaceFactory $shippingModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shippingConfigModel,
        \Magestore\Webpos\Helper\Data $helper
    )
    {
        $this->shippingMethodFactory = $shippingModel;
        $this->shippingConfigModel = $shippingConfigModel;
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
        $this->allowShippings = array('webpos_shipping', 'flatrate', 'freeshipping', 'tablerate');
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->getListCoreShippingMethods();
        $options = array();
        if (count($collection) > 0) {
            foreach ($collection as $code => $carrier) {
                if ($code == 'webpos_shipping')
                    continue;
                $title = $carrier->getConfigData('title') . ' - ' . $carrier->getConfigData('name');
                $options[] = array('value' => $code, 'label' => $title);
            }
        }
        return $options;
    }

    /**
     * get shipping methods for pos
     *
     * @return array
     */
    public function getPosShippingMethods()
    {
        $collection = $this->getListCoreShippingMethods();
        $shippingList = array();
        if (count($collection) > 0) {
            foreach ($collection as $code => $carrier) {
                if (!$this->isAllowOnWebPOS($code))
                    continue;
                $shippingModel = $this->shippingMethodFactory->create();
                $isDefault = 0;
                if ($code == $this->getDefaultShippingMethod()) {
                    $isDefault = 1;
                }
                $method_code = $code;
                if ($code == 'webpos_shipping') {
                    $method_code = 'storepickup';
                }
                $methodCode = $code . '_' . $method_code;
                $methodTitle = $carrier->getConfigData('title') . ' - ' . $carrier->getConfigData('name');
                $methodPrice = ($carrier->getConfigData('price') != null) ? (float)$carrier->getConfigData('price') : 0;
                $methodType = ($carrier->getConfigData('type') != null) ? $carrier->getConfigData('type') : '';
                $shipmentRequestType = $carrier->getConfigData('shipment_requesttype')
                    ? $carrier->getConfigData('shipment_requesttype') : 0;
                $conditionName = $carrier->getConfigData('condition_name') != null ?
                    $carrier->getConfigData('condition_name') : '';
                $methodDescription = $carrier->getConfigData('description') != null ?
                    $carrier->getConfigData('description') : '';
                $speCountriesAllow = $carrier->getConfigData('sallowspecific') ?
                    $carrier->getConfigData('sallowspecific') : 0;
                $methodSpecificerrmsg = $carrier->getConfigData('specificerrmsg') != null ?
                    $carrier->getConfigData('specificerrmsg') : '';
                $specificCountry = $carrier->getConfigData('specificcountry') ?
                    $carrier->getConfigData('specificcountry') : '';
                $handlingFee = $carrier->getConfigData('handling_fee') ? (float)$carrier->getConfigData('handling_fee') : 0;
                $handlingType = $carrier->getConfigData('handling_type') ?
                    $carrier->getConfigData('handling_type') : 'F';
                $handlingAction = $carrier->getConfigData('handling_action') ?
                    $carrier->getConfigData('handling_action') : 'O';
                $maxPackageWeight = $carrier->getConfigData('max_package_weight') ?
                    $carrier->getConfigData('max_package_weight') : 0;
                $includeVirtualPrice = $carrier->getConfigData('include_virtual_price') ?
                    $carrier->getConfigData('include_virtual_price') : 0;
                $freeShippingSubtotal = $carrier->getConfigData('free_shipping_subtotal') ?
                    $carrier->getConfigData('free_shipping_subtotal') : 0;
                $rates = [];
                if ($code == 'tablerate') {
                    /** @var \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate $resourceModel */
                    $resourceModel = \Magento\Framework\App\ObjectManager::getInstance()
                        ->create('Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate');
                    $connection = $resourceModel->getConnection();
                    $select = $connection->select()->from($resourceModel->getMainTable());
                    $result = $connection->fetchAll($select);
                    if (!empty($result)) {
                        $rates = $result;
                    }
                }
                $shippingModel->setCode($methodCode);
                $shippingModel->setTitle($methodTitle);
                $shippingModel->setPrice($methodPrice);
                $shippingModel->setType($methodType);
                $shippingModel->setShipmentRequestType($shipmentRequestType);
                $shippingModel->setConditionName($conditionName);
                $shippingModel->setDescription($methodDescription);
                $shippingModel->setErrorMessage($methodSpecificerrmsg);
                $shippingModel->setIsDefault($isDefault);
                $shippingModel->setSpecificCountriesAllow($speCountriesAllow);
                $shippingModel->setSpecificCountry($specificCountry);
                $shippingModel->setHandlingFee($handlingFee);
                $shippingModel->setHandlingType($handlingType);
                $shippingModel->setHandlingAction($handlingAction);
                $shippingModel->setMaxPackageWeight($maxPackageWeight);
                $shippingModel->setIncludeVirtualPrice($includeVirtualPrice);
                $shippingModel->setFreeShippingSubtotal($freeShippingSubtotal);
                $shippingModel->setRates($rates);
                $shippingList[] = $shippingModel->getData();
            }
        }
        return $shippingList;
    }

    public function getListCoreShippingMethods()
    {
        $collection = $this->shippingConfigModel->getAllCarriers($this->helper->getCurrentStoreView()->getId());
        $listMethod = [];
        foreach ($collection as $code => $carrier) {
            if (in_array($code, $this->allowShippings)) {
                $listMethod[$code] = $carrier;
            }
        }
        return $listMethod;
    }

    /**
     * get array of allow shipping methods
     * @return array
     */
    public function getAllowShippingMethods()
    {
        return $this->allowShippings;
    }

    /**
     * check shipping method for pos
     *
     * @param string $code
     * @return boolean
     */
    public function isAllowOnWebPOS($code)
    {
        $specificshipping = $this->scopeConfig->getValue(self::SPECIFIC_SHIPPING, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $specificshipping = explode(',', $specificshipping);
        $specificshipping[] = 'webpos_shipping';
        if (in_array($code, $specificshipping)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get default shipping method for pos
     *
     * @return string
     */
    public function getDefaultShippingMethod()
    {
        return 'webpos_shipping';
    }

}
