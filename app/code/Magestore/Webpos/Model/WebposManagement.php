<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model;

/**
 * Class SearchCriteria
 * @package Magestore\Webpos\Model
 */
class WebposManagement implements \Magestore\Webpos\Api\WebposManagementInterface
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
     * @todo it will be change after our develop POS Pro version
     * 
     * @return boolean
     */
    public function isWebposStandard()
    {
        return false;
    }
}
