<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Setup;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Cron recurring setup
 */
class InstallData implements InstallDataInterface
{
    /**
     *
     */
    const NOT_ENCODE_PASSWORD = 0;
    /**
     *
     */
    const DEFAULT_RESOURCE_ACCESS = 'Magestore_Appadmin::all';

    /**
     * @var \Magestore\Webpos\Model\Location\LocationFactory
     */
    protected $_locationFactory;

    /**
     * @var \Magestore\Webpos\Model\Pos\PosFactory
     */
    protected $_posFactory;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory
     */
    protected $_locationCollectionFactory;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    /**
     * @var \Magestore\Appadmin\Model\Staff\RoleFactory
     */
    protected $_roleFactory;
    /**
     * @var \Magestore\Appadmin\Model\Staff\StaffFactory
     */
    protected $_staffFactory;
    /**
     * @var \Magestore\Appadmin\Model\Staff\AuthorizationRuleFactory
     */
    protected $_authorizationRuleFactory;
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;


    /**
     * Recurring constructor.
     * @param \Magestore\Webpos\Model\Location\LocationFactory $locationFactory
     * @param \Magestore\Webpos\Model\Pos\PosFactory $posFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory $locationCollectionFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magestore\Appadmin\Model\Staff\RoleFactory $roleFactory
     * @param \Magestore\Appadmin\Model\Staff\AuthorizationRuleFactory $authorizationRuleFactory
     * @param \Magestore\Appadmin\Model\Staff\StaffFactory $staffFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magestore\Webpos\Model\Location\LocationFactory $locationFactory,
        \Magestore\Webpos\Model\Pos\PosFactory $posFactory,
        \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory $locationCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magestore\Appadmin\Model\Staff\RoleFactory $roleFactory,
        \Magestore\Appadmin\Model\Staff\AuthorizationRuleFactory $authorizationRuleFactory,
        \Magestore\Appadmin\Model\Staff\StaffFactory $staffFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\State $appState,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_locationFactory = $locationFactory;
        $this->_posFactory = $posFactory;
        $this->_locationCollectionFactory = $locationCollectionFactory;
        $this->moduleManager = $moduleManager;
        $this->_roleFactory = $roleFactory;
        $this->_authorizationRuleFactory = $authorizationRuleFactory;
        $this->_staffFactory = $staffFactory;
        $this->productMetadata = $productMetadata;
        $this->_appState = $appState;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var ObjectManager $objectManager */
        $objectManager = ObjectManager::getInstance();
        /** @var \Magestore\Webpos\Api\WebposManagementInterface $webposManagement */
        $webposManagement = $objectManager->get('Magestore\Webpos\Api\WebposManagementInterface');

        /* SAMPLE MULTI POS*/
        $locationDataSample2 = array(
            'name' => 'Location_MultiPOS',
            'street' => '6146 Honey Bluff Parkway',
            'city' => 'Calder',
            'region' => 'Michigan',
            'region_id' => 33,
            'country_id' => 'US',
            'country' => 'United State',
            'postcode' => '49628-7978',
            'description' => 'To distribute products for brick-and-mortar store'
        );

        if ($webposManagement->isMSIEnable()) {
            /** @var \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider */
            $defaultStockProvider = $objectManager->create('Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface');
            $locationDataSample2['stock_id'] = $defaultStockProvider->getId();
        }

        $locationSample2 = $this->_locationFactory->create()->load($locationDataSample2['name'], 'name');
        if (!$locationSample2->getId()){
            $locationSample2 = $this->_locationFactory->create()->setData($locationDataSample2)->save();
        }

        $posMultiPOSData1 = array(
            'pos_name' => 'POS_MultiPOS1',
            'location_id' => $locationSample2->getId(),
            'status' => '1'
        );

        $pos1 = $this->_posFactory->create()->load($posMultiPOSData1['pos_name'], 'pos_name');
        if (!$pos1->getId()){
            $pos1 = $this->_posFactory->create()->setData($posMultiPOSData1)->save();
        }

        $posMultiPOSData2 = array(
            'pos_name' => 'POS_MultiPOS2',
            'location_id' => $locationSample2->getId(),
            'status' => '1'
        );
        $pos2 = $this->_posFactory->create()->load($posMultiPOSData2['pos_name'], 'pos_name');
        if (!$pos2->getId()){
            $pos2 = $this->_posFactory->create()->setData($posMultiPOSData2)->save();
        }

        /* SAMPLE ONE POS ONE LOCATION*/

        $locationDataSample1 = array(
            'name' => 'Location_OnePOSOneLocation',
            'street' => '6146 Honey Bluff Parkway',
            'city' => 'Calder',
            'region' => 'Michigan',
            'region_id' => 33,
            'country_id' => 'US',
            'country' => 'United State',
            'postcode' => '49628-7978',
            'description' => 'To distribute products for brick-and-mortar store'
        );

        if (isset($locationDataSample2['stock_id'])) {
            $locationDataSample1['stock_id'] = $locationDataSample2['stock_id'];
        }

        $locationSample1 = $this->_locationFactory->create()->load($locationDataSample1['name'], 'name');
        if (!$locationSample1->getId()){
            $locationSample1 = $this->_locationFactory->create()->setData($locationDataSample1)->save();
        }

        $posData = array(
            'pos_name' => 'POS_OnePOSOneLocation',
            'location_id' => $locationSample1->getId(),
            'status' => '1'
        );

        $pos = $this->_posFactory->create()->load($posData['pos_name'], 'pos_name');
        if (!$pos->getId()){
            $pos = $this->_posFactory->create()->setData($posData)->save();
        }

        /*
         * Setup Role Data
         */
        $roleData = array(
            'name' => 'Role_AutoTest',
            'max_discount_percent' => 100
        );

        $role = $this->_roleFactory->create()->load($roleData['name'], 'name');
        if (!$role->getId()){
            $role = $this->_roleFactory->create()->setData($roleData)->save();
        }

        $authorizeRule = array(
            'role_id' => $role->getId(),
            'resource_id' => self::DEFAULT_RESOURCE_ACCESS
        );
        $this->_authorizationRuleFactory->create()->setData($authorizeRule)->save();

        $staffOnePosData = array(
            'role_id' => $role->getId(),
            'username' => 'staffonepos',
            'password' => 'admin123',
            'name' => 'Staff One POS One Location',
            'email' => 'staffonepos@example.com',
            'location_ids' => $locationSample1->getId(),
            'not_encode' => 0,
            'status' => 1
        );

        $staff1 = $this->_staffFactory->create()->load($staffOnePosData['username'], 'username');
        if (!$staff1->getId()){
            $staff1 = $this->_staffFactory->create()->setData($staffOnePosData)->save();
        }

        $staffMultiPosData = array(
            'role_id' => $role->getId(),
            'username' => 'staffmultipos',
            'password' => 'admin123',
            'name' => 'Staff Multi POS',
            'email' => 'staffmultipos@example.com',
            'location_ids' => $locationSample2->getId(),
            'not_encode' => 0,
            'status' => 1
        );

        $staff2 = $this->_staffFactory->create()->load($staffMultiPosData['username'], 'username');
        if (!$staff2->getId()){
            $staff2 = $this->_staffFactory->create()->setData($staffMultiPosData)->save();
        }

        $version = $this->productMetadata->getVersion();
        try{
            if(version_compare($version, '2.2.0', '>=')) {
                $this->_appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            } else {
                $this->_appState->setAreaCode('admin');
            }
        } catch(\Exception $e) {
            $this->_appState->getAreaCode();
        }
        $productId = null;
        try {
            $product = $this->productRepository->get('simple-option');
            $productId = $product->getId();
        } catch (\Exception $e) {

        }
        if(!$productId) {
            $this->createNewProduct();
        }

        //virtual product
        $virtualProductId = null;
        try {
            $product = $this->productRepository->get('virtual-test');
            $virtualProductId = $product->getId();
        } catch (\Exception $e) {

        }
        if(!$virtualProductId) {
            $this->createVirtualProduct();
        }

        //virtual product with custom option
        $virtualProductId = null;
        try {
            $product = $this->productRepository->get('virtual-test-co');
            $virtualProductId = $product->getId();
        } catch (\Exception $e) {

        }
        if(!$virtualProductId) {
            $this->createVirtualProductWithCustomOption();
        }
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function createNewProduct() {
        $product = $this->productFactory->create();
        $product->setSku('simple-option');
        $product->setName('Simple Product With Custom Option');
        $product->setAttributeSetId(4);
        $product->setStatus(1);
        $product->setWeight(10);
        $product->setVisibility(4);
        $product->setTaxClassId(0);
        $product->setTypeId('simple');
        $product->setPrice(100);
        $product->setStockData(
            array(
                'use_config_manage_stock' => 0,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 9999
            )
        );
        $product = $this->productRepository->save($product);

        // add custom option
        $option = [
            "sort_order"    => 1,
            "title"         => "Test custom option",
            "price_type"    => "fixed",
            "price"         => "5",
            "type"          => "field",
            "is_require"    => 1
        ];
        $product->setHasOptions(1);
        $product->setCanSaveCustomOptions(true);
        $option = $this->objectManager->create('\Magento\Catalog\Model\Product\Option')
            ->setProductId($product->getId())
            ->setStoreId($product->getStoreId())
            ->addData($option);
        $option->save();
        $product->addOption($option);
        $this->productRepository->save($product);
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function createVirtualProduct() {
        $product = $this->productFactory->create();
        $product->setSku('virtual-test');
        $product->setName('Virtual test');
        $product->setAttributeSetId(4);
        $product->setStatus(1);
        $product->setWeight(10);
        $product->setVisibility(4);
        $product->setTaxClassId(0);
        $product->setTypeId('virtual');
        $product->setPrice(100);
//        $product->setWebposVisible(1);
        $product->setStockData(
            array(
                'use_config_manage_stock' => 0,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 9999
            )
        );
        $product = $this->productRepository->save($product);
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function createVirtualProductWithCustomOption() {
        $product = $this->productFactory->create();
        $product->setSku('virtual-test-co');
        $product->setName('Virtual test CO');
        $product->setAttributeSetId(4);
        $product->setStatus(1);
        $product->setWeight(10);
        $product->setVisibility(4);
        $product->setTaxClassId(0);
        $product->setTypeId('virtual');
        $product->setPrice(100);
//        $product->setWebposVisible(1);
        $product->setStockData(
            array(
                'use_config_manage_stock' => 0,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 9999
            )
        );
        $product = $this->productRepository->save($product);

        // add custom option
        $option = [
            "sort_order"    => 1,
            "title"         => "Test custom option",
            "price_type"    => "fixed",
            "price"         => "5",
            "type"          => "field",
            "is_require"    => 1
        ];
        $product->setHasOptions(1);
        $product->setCanSaveCustomOptions(true);
        $option = $this->objectManager->create('\Magento\Catalog\Model\Product\Option')
            ->setProductId($product->getId())
            ->setStoreId($product->getStoreId())
            ->addData($option);
        $option->save();
        $product->addOption($option);
        $this->productRepository->save($product);
    }
}
