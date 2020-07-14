<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * Upgrade the Catalog module DB scheme
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $_eavAttribute;

    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var \Magestore\Webpos\Helper\Product\CustomSale $customSaleHelper
     */
    protected $customSaleHelper;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface
     */
    protected $stockManagement;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param \Magestore\Webpos\Helper\Product\CustomSale $customSaleHelper
     * @param \Magento\Framework\App\State $appState
     * @param \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement
     * @param \Magento\Framework\Module\Manager $moduleManager
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        \Magestore\Webpos\Helper\Product\CustomSale $customSaleHelper,
        \Magento\Framework\App\State $appState,
        \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->objectManager = $objectManager;
        $this->_eavAttribute = $eavAttribute;
        $this->productMetadata = $productMetadata;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->customSaleHelper = $customSaleHelper;
        $this->_appState = $appState;
        $this->stockManagement = $stockManagement;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $version = $this->productMetadata->getVersion();

        try {
            if (version_compare($version, '2.2.0', '>=') || $version === 'No version set (parsed as 1.0.0)') {
                $this->_appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            } else {
                $this->_appState->setAreaCode('admin');
            }
        } catch (\Exception $e) {
            $this->_appState->getAreaCode();
        }

        if (version_compare($context->getVersion(), '0.1.0.7', '<')) {
            // create attribute
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create();
            /**
             * Remove attribute webpos_visible
             */
            //Find these in the eav_entity_type table
            $action = $this->objectManager->get(\Magento\Catalog\Model\ResourceModel\Product\Action::class);
            $attribute = $action->getAttribute('webpos_visible');
            if ($attribute) {
                $entityTypeId = $this->objectManager
                    ->create(\Magento\Eav\Model\Config::class)
                    ->getEntityType(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE)
                    ->getEntityTypeId();
                $eavSetup->removeAttribute($entityTypeId, 'webpos_visible');
            }

            $eavSetup->removeAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'webpos_visible'
            );

            /**
             * Add attributes to the eav/attribute
             */
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'webpos_visible',
                [
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Visible on POS',
                    'input' => 'boolean',
                    'class' => '',
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_NO,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );

            // add default data for attribute
            $attributeId = $this->_eavAttribute->getIdByCode('catalog_product', 'webpos_visible');
            $action = $this->objectManager->create(\Magento\Catalog\Model\ResourceModel\Product\Action::class);
            $connection = $action->getConnection();
            $table = $setup->getTable('catalog_product_entity_int');
            //set invisible for default
            $productCollection = $this->objectManager->create(
                \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection::class
            );
            $visibleInSite = $this->objectManager->create(\Magento\Catalog\Model\Product\Visibility::class)
                ->getVisibleInSiteIds();

            $productCollection->addAttributeToFilter('visibility', ['in' => $visibleInSite]);

            $version = $this->productMetadata->getVersion();
            $edition = $this->productMetadata->getEdition();
            foreach ($productCollection->getAllIds() as $productId) {
                if (($edition == 'Enterprise' || $edition == 'B2B')
                    && version_compare($version, '2.1.5', '>=')
                ) {
                    $data = [
                        'attribute_id' => $attributeId,
                        'store_id' => 0,
                        'row_id' => $productId,
                        'value' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES
                    ];
                } else {
                    $data = [
                        'attribute_id' => $attributeId,
                        'store_id' => 0,
                        'entity_id' => $productId,
                        'value' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES
                    ];
                }
                $connection->insertOnDuplicate($table, $data, ['value']);
            }
        }

        if (version_compare($context->getVersion(), '0.1.0.9', '<')) {
            $eavSetup = $this->eavSetupFactory->create();
            // add customer_attribute to customer
            $eavSetup->addAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'customer_telephone',
                [
                    'type' => 'varchar',
                    'label' => 'Customer Telephone',
                    'input' => 'text',
                    'required' => false,
                    'visible' => false,
                    'system' => 0,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'sort_order' => '200'
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.1.0.20', '<')) {
            $this->customSaleHelper->createProduct($setup);
        }

        if (version_compare($context->getVersion(), '1.1.1.1', '<')) {
            /** @var \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider */
            if ($this->moduleManager->isEnabled('Magento_InventoryCatalogApi')) {
                $defaultStockProvider = $this->objectManager
                    ->get(\Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface::class);
                $this->stockManagement->addCustomSaleToStock($defaultStockProvider->getId());
            }
        }

        if (version_compare($context->getVersion(), '1.2.0.7', '<')) {
            $eavSetup = $this->eavSetupFactory->create();
            // add customer_attribute to customer
            $eavSetup->addAttribute(
                \Magento\Customer\Api\AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
                'sub_id',
                [
                    'type' => 'text',
                    'label' => 'Address sub id',
                    'input' => 'text',
                    'required' => false,
                    'visible' => false,
                    'system' => 0,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'sort_order' => '210'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.0.11', '<')) {
            $eavSetup = $this->eavSetupFactory->create();
            // add customer_attribute to customer
            $eavSetup->addAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'tmp_customer_id',
                [
                    'type' => 'text',
                    'label' => 'Temp Customer ID',
                    'input' => 'text',
                    'required' => false,
                    'visible' => false,
                    'system' => 1,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'sort_order' => '210'
                ]
            );
        }

        $setup->endSetup();
    }
}
