<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Location;

use Magestore\Webpos\Api\Data\Location\LocationInterface;

/**
 * Model Location
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Location extends \Magento\Framework\Model\AbstractModel implements LocationInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'webpos_location';
    /**
     * @var \Magento\Directory\Model\Country
     */
    protected $country;
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;
    /**
     * @var \Magestore\Webpos\Api\Data\Staff\Login\Location\AddressInterface
     */
    protected $addressInterface;
    /**
     * @var \Magento\Customer\Api\Data\RegionInterface
     */
    protected $region;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface
     */
    protected $stockManagement;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;
    /**
     * @var \Magestore\Appadmin\Api\Event\DispatchServiceInterface
     */
    protected $dispatchService;
    /**
     * @var \Magestore\Webpos\Api\Location\LocationRepositoryInterface
     */
    protected $locationRepository;

    protected $currentData;

    /**
     * Location constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Webpos\Model\ResourceModel\Location\Location $resource
     * @param \Magestore\Webpos\Model\ResourceModel\Location\Location\Collection $resourceCollection
     * @param \Magento\Directory\Model\Country $country
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magestore\Webpos\Api\Data\Staff\Login\Location\AddressInterface $addressInterface
     * @param \Magento\Customer\Api\Data\RegionInterface $region
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService
     * @param \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Model\ResourceModel\Location\Location $resource,
        \Magestore\Webpos\Model\ResourceModel\Location\Location\Collection $resourceCollection,
        \Magento\Directory\Model\Country $country,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magestore\Webpos\Api\Data\Staff\Login\Location\AddressInterface $addressInterface,
        \Magento\Customer\Api\Data\RegionInterface $region,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService,
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->country = $country;
        $this->regionFactory = $regionFactory;
        $this->addressInterface = $addressInterface;
        $this->region = $region;
        $this->moduleManager = $moduleManager;
        $this->stockManagement = $stockManagement;
        $this->storeManager = $storeManager;
        $this->storeFactory = $storeFactory;
        $this->dispatchService = $dispatchService;
        $this->locationRepository = $locationRepository;
    }

    /**
     * @inheritdoc
     */
    public function getLocationId()
    {
        if ($this->moduleManager->isEnabled('Magestore_InventorySuccess')) {
            return $this->getData(self::WAREHOUSE_ID);
        } else {
            return $this->getData(self::LOCATION_ID);
        }
    }

    /**
     * @inheritdoc
     */
    public function setLocationId($locationId)
    {
        if ($this->moduleManager->isEnabled('Magestore_InventorySuccess')) {
            return $this->setData(self::WAREHOUSE_ID, $locationId);
        } else {
            return $this->setData(self::LOCATION_ID, $locationId);
        }
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        if ($this->moduleManager->isEnabled('Magestore_InventorySuccess')) {
            return $this->getData(self::WAREHOUSE_ID);
        } else {
            return $this->getData(self::LOCATION_ID);
        }
    }

    /**
     * @inheritdoc
     */
    public function setId($locationId)
    {
        if ($this->moduleManager->isEnabled('Magestore_InventorySuccess')) {
            return $this->setData(self::WAREHOUSE_ID, $locationId);
        } else {
            return $this->setData(self::LOCATION_ID, $locationId);
        }
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        if ($this->moduleManager->isEnabled('Magestore_InventorySuccess')) {
            return $this->getData(self::WAREHOUSE_NAME);
        } else {
            return $this->getData(self::NAME);
        }
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        if ($this->moduleManager->isEnabled('Magestore_InventorySuccess')) {
            return $this->setData(self::WAREHOUSE_NAME, $name);
        } else {
            return $this->setData(self::NAME, $name);
        }
    }

    /**
     * Get location code
     *
     * @return string
     * @api
     */
    public function getLocationCode()
    {
        return $this->getData(self::LOCATION_CODE);
    }

    /**
     * Set location code
     *
     * @param string $locationCode
     * @return $this
     * @api
     */
    public function setLocationCode($locationCode)
    {
        return $this->setData(self::LOCATION_CODE, $locationCode);
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritdoc
     */
    public function getTelephone()
    {
        if ($telephone = $this->getData(self::TELEPHONE)) {
            return $telephone;
        } else {
            return "";
        }
    }

    /**
     * @inheritdoc
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritdoc
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * @inheritdoc
     */
    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * @inheritdoc
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * @inheritdoc
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * @inheritdoc
     */
    public function getRegion()
    {
        return $this->getData(self::REGION);
    }

    /**
     * @inheritdoc
     */
    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * @inheritdoc
     */
    public function getRegionId()
    {
        return $this->getData(self::REGION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRegionId($regionId)
    {
        return $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * @inheritdoc
     */
    public function getCountry()
    {
        return $this->getData(self::COUNTRY);
    }

    /**
     * @inheritdoc
     */
    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }

    /**
     * @inheritdoc
     */
    public function getCountryId()
    {
        return $this->getData(self::COUNTRY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCountryId($countryId)
    {
        return $this->setData(self::COUNTRY_ID, $countryId);
    }

    /**
     * @inheritdoc
     */
    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    /**
     * @inheritdoc
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * @inheritdoc
     */
    public function getAddress()
    {
        $street = $this->getStreet();
        $city = $this->getCity();
        $region = $this->getRegion();
        $regionId = $this->getRegionId();
        $country = $this->getCountry();
        $countryId = $this->getCountryId();
        $postcode = $this->getPostcode();

        $regionCode = $region;
        if ($regionId) {
            $regionCode = $this->regionFactory->create()->load($regionId)->getCode();
        }

        $regionObject = $this->region;
        $regionObject->setRegion($region);
        $regionObject->setRegionCode($regionCode);
        $regionObject->setRegionId($regionId);

        $address = [
            'street' => $street,
            'city' => $city,
            'region' => $regionObject,
            'region_id' => $regionId,
            'country_id' => $countryId,
            'country' => $country,
            'postcode' => $postcode,
        ];
        return $this->addressInterface->setData($address);
    }

    /**
     * @inheritdoc
     */
    public function setAddress($address)
    {
        return $this->getData('address', $address);
    }

    /**
     * @inheritdoc
     */
    public function getWarehouseId()
    {
        return $this->getData(self::WAREHOUSE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setWarehouseId($warehouseId)
    {
        return $this->setData(self::WAREHOUSE_ID, $warehouseId);
    }

    /**
     * Get Stock Id
     *
     * @return int|null
     */
    public function getStockId()
    {
        return $this->getData(self::STOCK_ID) > 0 ? (int)$this->getData(self::STOCK_ID) : null;
    }

    /**
     * Set Stock Id
     *
     * @param int|null $stockId
     * @return LocationInterface
     */
    public function setStockId($stockId)
    {
        return $this->setData(self::STOCK_ID, $stockId);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getStoreCode()
    {
        $storeId = $this->getStoreId();
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeFactory->create()->load($storeId);
        if (!$storeId || !$store) {
            return $this->storeManager->getDefaultStoreView()->getCode();
        }
        return $store->getCode();
    }

    /**
     * @inheritDoc
     */
    public function setStoreCode($storeCode)
    {
        return $this->setData(self::STORE_CODE, $storeCode);
    }

    /**
     * @inheritDoc
     */
    public function getWebsiteId()
    {
        $storeId = $this->getStoreId();
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeFactory->create()->load($storeId);
        if (!$storeId || !$store) {
            return $this->storeManager->getDefaultStoreView()->getWebsiteId();
        }
        return $store->getWebsiteId();
    }

    /**
     * @inheritDoc
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * Before delete
     *
     * @return Location
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {
        $this->forceChangeLocationData($this->getId());
        return parent::beforeDelete();
    }

    /**
     * Force change location
     *
     * @param int $locationId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function forceChangeLocationData($locationId)
    {
        /** @var \Magestore\Webpos\Model\ResourceModel\Location\Location $resourceLocation */
        $resourceLocation = $this->getResource();
        $select = $resourceLocation->getConnection()->select();
        $select->from(['e' => $resourceLocation->getMainTable()]);
        $select->join(
            ['pos' => $resourceLocation->getTable('webpos_pos')],
            'pos.location_id = e.' . $resourceLocation->getIdFieldName(),
            []
        );
        $select->where('pos.location_id = ' . $locationId);
        $select->columns('pos.staff_id');

        $data = $resourceLocation->getConnection()->fetchAll($select);
        foreach ($data as $datum) {
            if (!$datum['staff_id']) {
                continue;
            }
            // dispatch event force sign out
            $this->dispatchService->dispatchEventForceSignOut($datum['staff_id']);
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        if ($regionId = $this->getRegionId()) {
            $rgModel = $this->regionFactory->create()->load($regionId);
            if ($rgModel->getId()) {
                $this->setRegion($rgModel->getName());
            }
        }

        $countryId = $this->getCountryId();
        $countryModel = $this->country->loadByCode($countryId);
        $this->setCountry($countryModel->getName());

        // set data object before save
        if (!$this->isObjectNew()) {
            $currentObject = $this->locationRepository->getById($this->getId());
            $this->currentData = $currentObject->getData();
        }

        return parent::beforeSave();
    }

    /**
     * Processing object after save data
     *
     * @return $this
     */
    public function afterSave()
    {
        $stockId = $this->getStockId();
        if ($stockId) {
            $this->stockManagement->addCustomSaleToStock($stockId);
        }

        if (!$this->isObjectNew()) {
            if ($this->hasDataChangeForField(self::STORE_ID)) {
                $this->forceChangeLocationData($this->getId());
            }
        }

        return parent::afterSave();
    }

    /**
     * Check if data has change for field
     *
     * @param string $fieldName
     * @return bool
     */
    public function hasDataChangeForField($fieldName)
    {
        if (!isset($this->currentData[$fieldName])) {
            return $this->getData($fieldName) == null ? false : true;
        }
        return !($this->currentData[$fieldName] == $this->getData($fieldName));
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Location\LocationExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
