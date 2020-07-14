<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSumary;

use Magento\InventoryApi\Api\SourceRepositoryInterface;

/**
 * Class Supplier
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSumary
 */
class Supplier extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface
     */
    protected $returnOrderRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface
     */
    protected $returnOrder;

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
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface $returnOrderRepository,
        \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        SourceRepositoryInterface $sourceRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->returnOrderRepository = $returnOrderRepository;
        $this->supplierRepository = $supplierRepository;
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->returnOrder = $this->getCurrentReturnOrder();
        $this->sourceRepository = $sourceRepository;
    }

    protected $_template = 'Magestore_PurchaseOrderSuccess::returnorder/form/returnsumary/supplier.phtml';

    /**
     * Get current return order
     *
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface
     */
    public function getCurrentReturnOrder(){
        $returnOrder = $this->registry->registry('current_return_order');
        if(!$returnOrder || !$returnOrder->getId())
            $returnOrder = $this->returnOrderRepository->get($this->getRequest()->getParam('id'));
        return $returnOrder;
    }

    public function getSupplierInformation(){
        $supplierId = $this->returnOrder->getSupplierId();
        try{
            $supplier = $this->supplierRepository->getById($supplierId);
            $this->setData('current_supplier',$supplier);
        }catch (\Exception $exception){
            return '';
        }
        $html = $supplier->getSupplierName() . ' (' . $supplier->getSupplierCode() . ')';
        $html.= '<br/>'. $this->getFormatedAddress($supplier->getData());
        return $html;
    }

    public function getWarehouseInformation(){
        $warehouseId = $this->returnOrder->getWarehouseId();
        try{
            $warehouse = $this->sourceRepository->get($warehouseId);
            $this->setData('current_warehouse',$warehouse);
        }catch (\Exception $exception){
            return '';
        }
        $html = $warehouse->getName() . ' (' . $warehouse->getSourceCode() . ')';
        $html.= '<br/>'. $this->getFormatedAddress($warehouse->getData());
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
    public function getFormatedAddress($data)
    {
        $address = '';
        $region = $this->getRegion($data);
        $postCode = $data['postcode'];
        $city = $data['city'];
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
        $address .= $data['street'] . '<br/>';
        $address .= implode(', ', $cityRegionZip) . '<br/>';
        $address .= $this->getCountry($data);

        return $address;
    }

    public function getStreetCity($data){
        $result = [];
        $street = $data['street'];
        $city = $data['city'];
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
    public function getPostCodeRegionCountry($data){
        $result = [];
        $postCode = $data['postcode'];
        $region = $this->getRegion($data);
        $country = $this->getCountry($data);
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
    public function getCountry($data){
        if($data['country_id'])
            return $this->countryFactory->create()->loadByCode(
                $data['country_id']
            )->getName();
        return '';
    }

    /**
     * @return string
     */
    public function getRegion($data){
        if($data['region_id'])
            return $this->regionFactory->create()->load($data['region_id'])->getName();
        return $data['region'];
    }

    /**
     * Get return date of PO
     *
     * @return string
     */
    public function getPurchaseDate()
    {
        $timezone = new \DateTimeZone($this->_localeDate->getConfigTimezone());
        $createdDate = \DateTime::createFromFormat('Y-m-d', $this->getCurrentReturnOrder()->getReturnedAt(), $timezone);
        return $this->formatDate(
            $createdDate,
            \IntlDateFormatter::LONG
        );
    }
}