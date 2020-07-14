<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Setup;


use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     *
     */
    const IS_ACTIVE = 1;
    /**
     *
     */
    const NOT_ENCODE_PASSWORD = 1;

    const DEFAULT_RESOURCE_ACCESS = 'Magestore_Appadmin::all';

    /**
     * @var \Magestore\Appadmin\Model\Staff\RoleFactory
     */
    protected $_roleFactory;

    /**
     * @var \Magestore\Appadmin\Model\Staff\StaffFactory
     */
    protected $_staffFactory;

    /**
     * @var \Magento\User\Model\ResourceModel\User\CollectionFactory
     */
    protected $_userCollectionFactory;
    /**
     * @var \Magestore\Appadmin\Model\Staff\AuthorizationRuleFactory
     */
    protected $_authorizationRuleFactory;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory
     */
    protected $_locationCollectionFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * InstallData constructor.
     * @param \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory
     * @param \Magestore\Appadmin\Model\Staff\RoleFactory $roleFactory
     * @param \Magestore\Appadmin\Model\Staff\AuthorizationRuleFactory $authorizationRuleFactory
     * @param \Magestore\Appadmin\Model\Staff\StaffFactory $staffFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory,
        \Magestore\Appadmin\Model\Staff\RoleFactory $roleFactory,
        \Magestore\Appadmin\Model\Staff\AuthorizationRuleFactory $authorizationRuleFactory,
        \Magestore\Appadmin\Model\Staff\StaffFactory $staffFactory,
        \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory $locationCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager
    )
    {
        $this->_userCollectionFactory = $userCollectionFactory;
        $this->_roleFactory = $roleFactory;
        $this->_authorizationRuleFactory = $authorizationRuleFactory;
        $this->_staffFactory = $staffFactory;
        $this->_locationCollectionFactory = $locationCollectionFactory;
        $this->_moduleManager = $moduleManager;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /*
         * Setup Role Data
         */
        $roleData = array(
            'name' => 'Cashier'
        );
        $role = $this->_roleFactory->create()->setData($roleData)->save();

        $authorizeRule = array(
            'role_id' => $role->getId(),
            'resource_id' => self::DEFAULT_RESOURCE_ACCESS
        );
        $this->_authorizationRuleFactory->create()->setData($authorizeRule)->save();

        /*
         * Setup Staff Data
         */
        $userModel = $this->_userCollectionFactory->create()->addFieldToFilter('is_active', self::IS_ACTIVE)->getFirstItem();
        /** @var \Magestore\Webpos\Model\Location\Location $locationModel */
        $locationModel = $this->_locationCollectionFactory->create()
            ->getFirstItem();
        $locationId = $locationModel->getId();
        if (!$locationId) {
            if ($this->_moduleManager->isEnabled('Magestore_InventorySuccess')) {
                $locationId = 2;
            } else {
                $locationId = 1;
            }
        }
        if ($userModel->getId()) {
            $staffData = array(
                'role_id' => $role->getId(),
                'username' => $userModel->getUsername(),
                'password' => $userModel->getPassword(),
                'name' => $userModel->getFirstname() . ' ' . $userModel->getLastname(),
                'email' => $userModel->getEmail(),
                'location_ids' => $locationId,
                'not_encode' => self::NOT_ENCODE_PASSWORD,
                'status' => self::IS_ACTIVE
            );
            $this->_staffFactory->create()->setData($staffData)->save();
        }

        $setup->endSetup();
    }
}