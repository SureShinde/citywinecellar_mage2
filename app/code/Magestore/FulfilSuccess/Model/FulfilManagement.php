<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Model;
/**
 * Class FulfilManagement
 * @package Magestore\FulfilSuccess\Model
 */
class FulfilManagement implements \Magestore\FulfilSuccess\Api\FulfilManagementInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * WebposManagement constructor.
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager
    )
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return boolean
     */
    public function isMSIEnable()
    {
        return $this->moduleManager->isEnabled('Magento_Inventory') &&
        $this->moduleManager->isEnabled('Magento_InventoryApi');
    }

    /**
     * @return boolean
     */
    public function isInventorySuccessEnable()
    {
        return $this->moduleManager->isEnabled('Magestore_InventorySuccess');
    }
}
