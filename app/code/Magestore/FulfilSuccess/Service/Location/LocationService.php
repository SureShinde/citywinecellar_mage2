<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\Location;

use Magestore\FulfilSuccess\Service\Location\LocationServiceInterface;

class LocationService implements LocationServiceInterface
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $allowedWarehouses = [];

    /**
     */
    protected $permissionManagement;

    /**
     * @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface
     */
    protected $fulfilManagement;

    /**
     * LocationService constructor.
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement
    )
    {
        $this->session = $session;
        $this->objectManager = $objectManager;
        $this->fulfilManagement = $fulfilManagement;
    }

    /**
     * Get currently Warehouse Id which user is working on
     *
     * @return int
     */
    public function getCurrentWarehouseId()
    {
        $warehouseId = $this->session->getData(self::CURRENT_WAREHOUSE_SESSION_ID);
        if (!$warehouseId) {
            $allowedWarehouses = $this->getAllowedWarehouses();
            $warehouseId = reset($allowedWarehouses);
        }

        return $warehouseId;
    }

    /**
     * Get Ids of allowed Warehouses which user can process pick requests
     *
     * @param string $permissionResource
     * @return array
     */
    public function getAllowedWarehouses($permissionResource = null)
    {
        if (!count($this->allowedWarehouses)) {
            if ($this->fulfilManagement->isMSIEnable()) {
                /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
                $sourceRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceRepositoryInterface');
                $sources = $sourceRepository->getList()->getItems();
                foreach ($sources as $source) {
                    if ($source->isEnabled()) {
                        $this->allowedWarehouses[] = $source->getSourceCode();
                    }
                }
                return $this->allowedWarehouses;
            } else if ($this->fulfilManagement->isInventorySuccessEnable()) {
                $permissionResource = $permissionResource ?
                    $permissionResource :
                    \Magestore\FulfilSuccess\Api\PermissionManagementInterface::PICK_ITEM;
                $collection = $this->objectManager
                    ->create('Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Collection');
                $collection = $this->objectManager
                    ->create('Magestore\InventorySuccess\Api\Permission\PermissionManagementInterface')
                    ->filterPermission($collection, $permissionResource);
                $this->allowedWarehouses = $collection->getAllIds();
            }
        }
        return $this->allowedWarehouses;
    }

}