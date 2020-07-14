<?php

namespace Magestore\PurchaseOrderSuccess\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/* For get RoleType and UserType for create Role   */;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\UserContextInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Authorization\Model\RoleFactory
     */
    private $roleFactory;

    /**
     * @var \Magento\Authorization\Model\RulesFactory
     */
    private $rulesFactory;

    /**
     * InstallData constructor.
     * @param \Magento\Authorization\Model\RoleFactory $roleFactory
     * @param \Magento\Authorization\Model\RulesFactory $rulesFactory
     */
    public function __construct(
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\Authorization\Model\RulesFactory $rulesFactory
    ) {
        $this->roleFactory = $roleFactory;
        $this->rulesFactory = $rulesFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.3', '<')) {
//            $this->addPurchaseRole();
        }
    }

    public function addPurchaseRole() {
        /**
         * create rule Purchase Manager
         *
         * @var \Magento\Authorization\Model\Role $role
         */
        $role = $this->roleFactory->create();
        $role->setName('Purchase Manager')
        ->setPid(0)
        ->setRoleType(RoleGroup::ROLE_TYPE)
            ->setUserType(UserContextInterface::USER_TYPE_ADMIN);
        $role->save();

        $resource=['Magento_Backend::admin',
            'Magestore_OrderSuccess::all',
            'Magestore_InventorySuccess::inventory',
            'Magestore_InventorySuccess::warehouse',
            'Magestore_InventorySuccess::warehouse_stock_view',
            'Magestore_InventorySuccess::warehouse_list',
            'Magestore_InventorySuccess::warehouse_view',
            'Magestore_SupplierSuccess::supplier',
            'Magestore_SupplierSuccess::supplier_listing',
            'Magestore_SupplierSuccess::view_supplier',
            'Magestore_SupplierSuccess::supplier_pricinglist',
            'Magestore_SupplierSuccess::supplier_pricinglist_edit',
            'Magestore_PurchaseOrderSuccess::purchase_order',
            'Magestore_PurchaseOrderSuccess::manage_return_order',
            'Magestore_PurchaseOrderSuccess::list_return_order',
            'Magestore_PurchaseOrderSuccess::new_return_order',
            'Magestore_PurchaseOrderSuccess::view_return_order',
            'Magestore_PurchaseOrderSuccess::save_return_order',
            'Magestore_PurchaseOrderSuccess::send_return_order_request',
            'Magestore_PurchaseOrderSuccess::print_return_order',
            'Magestore_PurchaseOrderSuccess::cancel_return_order',
            'Magestore_PurchaseOrderSuccess::delete_return_order',
            'Magestore_PurchaseOrderSuccess::confirm_return_order',
            'Magestore_PurchaseOrderSuccess::complete_return_order',
            'Magestore_PurchaseOrderSuccess::transferred_return_order',
            'Magestore_PurchaseOrderSuccess::manage_quotation',
            'Magestore_PurchaseOrderSuccess::list_quotation',
            'Magestore_PurchaseOrderSuccess::new_quotation',
            'Magestore_PurchaseOrderSuccess::view_quotation',
            'Magestore_PurchaseOrderSuccess::convert_quotation',
            'Magestore_PurchaseOrderSuccess::confirm_quotation',
            'Magestore_PurchaseOrderSuccess::revert_quotation',
            'Magestore_PurchaseOrderSuccess::manage_purchase_order',
            'Magestore_PurchaseOrderSuccess::list_purchase_order',
            'Magestore_PurchaseOrderSuccess::new_purchase_order',
            'Magestore_PurchaseOrderSuccess::view_purchase_order',
            'Magestore_PurchaseOrderSuccess::save_purchase_order',
            'Magestore_PurchaseOrderSuccess::send_purchase_order_request',
            'Magestore_PurchaseOrderSuccess::print_purchase_order',
            'Magestore_PurchaseOrderSuccess::cancel_purchase_order',
            'Magestore_PurchaseOrderSuccess::delete_purchase_order',
            'Magestore_PurchaseOrderSuccess::confirm_purchase_order',
            'Magestore_PurchaseOrderSuccess::complete_purchase_order',
            'Magestore_PurchaseOrderSuccess::received_purchase_order',
            'Magestore_PurchaseOrderSuccess::returned_purchase_order',
            'Magestore_PurchaseOrderSuccess::transferred_purchase_order',
            'Magestore_PurchaseOrderSuccess::manage_invoice_purchase_order',
            'Magestore_PurchaseOrderSuccess::invoice_purchase_order',
            'Magestore_PurchaseOrderSuccess::invoice_purchase_order_view',
            'Magestore_PurchaseOrderSuccess::invoice_purchase_order_payment',
            'Magestore_PurchaseOrderSuccess::invoice_purchase_order_refund',
            'Magestore_PurchaseOrderSuccess::settings',
            'Magento_Catalog::catalog',
            'Magento_Catalog::catalog_inventory',
            'Magento_Catalog::products',
            'Magento_Catalog::categories',
            'Magento_Backend::myaccount',
            'Magento_Backend::stores',
            'Magento_Backend::stores_settings',
            'Magento_Config::config',
            'Magestore_PurchaseOrderSuccess::configuration'
        ];

        $this->rulesFactory->create()->setRoleId($role->getId())->setResources($resource)->saveRel();


        /**
         * create rule Purchase staff
         *
         * @var \Magento\Authorization\Model\Role $role
         */
        $role = $this->roleFactory->create();
        $role->setName('Purchase Staff')
        ->setPid(0)
        ->setRoleType(RoleGroup::ROLE_TYPE)
            ->setUserType(UserContextInterface::USER_TYPE_ADMIN);
        $role->save();

        $resource=['Magento_Backend::admin',
            'Magestore_OrderSuccess::all',
            'Magestore_InventorySuccess::inventory',
            'Magestore_InventorySuccess::warehouse',
            'Magestore_InventorySuccess::warehouse_stock_view',
            'Magestore_InventorySuccess::warehouse_list',
            'Magestore_InventorySuccess::warehouse_view',
            'Magestore_PurchaseOrderSuccess::purchase_order',
            'Magestore_PurchaseOrderSuccess::manage_return_order',
            'Magestore_PurchaseOrderSuccess::list_return_order',
            'Magestore_PurchaseOrderSuccess::new_return_order',
            'Magestore_PurchaseOrderSuccess::view_return_order',
            'Magestore_PurchaseOrderSuccess::save_return_order',
            'Magestore_PurchaseOrderSuccess::send_return_order_request',
            'Magestore_PurchaseOrderSuccess::print_return_order',
            'Magestore_PurchaseOrderSuccess::transferred_return_order',
            'Magestore_PurchaseOrderSuccess::manage_quotation',
            'Magestore_PurchaseOrderSuccess::list_quotation',
            'Magestore_PurchaseOrderSuccess::new_quotation',
            'Magestore_PurchaseOrderSuccess::view_quotation',
            'Magestore_PurchaseOrderSuccess::confirm_quotation',
            'Magestore_PurchaseOrderSuccess::manage_purchase_order',
            'Magestore_PurchaseOrderSuccess::list_purchase_order',
            'Magestore_PurchaseOrderSuccess::new_purchase_order',
            'Magestore_PurchaseOrderSuccess::view_purchase_order',
            'Magestore_PurchaseOrderSuccess::save_purchase_order',
            'Magestore_PurchaseOrderSuccess::send_purchase_order_request',
            'Magestore_PurchaseOrderSuccess::print_purchase_order',
            'Magestore_PurchaseOrderSuccess::received_purchase_order',
            'Magestore_PurchaseOrderSuccess::returned_purchase_order',
            'Magestore_PurchaseOrderSuccess::transferred_purchase_order',
            'Magestore_PurchaseOrderSuccess::manage_invoice_purchase_order',
            'Magestore_PurchaseOrderSuccess::invoice_purchase_order',
            'Magestore_PurchaseOrderSuccess::invoice_purchase_order_view',
            'Magento_Backend::myaccount'
        ];

        $this->rulesFactory->create()->setRoleId($role->getId())->setResources($resource)->saveRel();
    }
}