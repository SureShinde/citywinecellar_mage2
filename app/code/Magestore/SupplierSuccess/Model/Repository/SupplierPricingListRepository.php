<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Model\Repository;

use Magestore\SupplierSuccess\Api\Data;
use Magestore\SupplierSuccess\Api\SupplierPricingListRepositoryInterface;
use Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList as ResourceSupplierPricingList;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class BlockRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SupplierPricingListRepository implements SupplierPricingListRepositoryInterface
{
    /**
     * @var resourceSupplierPricingList|ResourceSupplierPricingList
     */
    protected $resourceSupplierPricingList;

    /**
     * @var \Magestore\SupplierSuccess\Model\Supplier\PricingListFactory
     */
    protected $supplierPricingListFactory;

    /**
     * SupplierPricingListRepository constructor.
     * @param ResourceSupplierPricingList $resource
     * @param \Magestore\SupplierSuccess\Model\Supplier\PricingListFactory $supplierPricingListFactory
     */
    public function __construct(
        ResourceSupplierPricingList $resource,
        \Magestore\SupplierSuccess\Model\Supplier\PricingListFactory $supplierPricingListFactory
    ) {
        $this->resourceSupplierPricingList = $resource;
        $this->supplierPricingListFactory = $supplierPricingListFactory;
    }

    /**
     * Save supplier pricelist.
     *
     * @param \Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface $supplierPricingList
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface $supplierPricingList)
    {
        try {
            $this->resourceSupplierPricingList->save($supplierPricingList);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $supplierPricingList;
    }

    /**
     * Retrieve supplier pricelist.
     *
     * @param int $supplierPricingListId
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($supplierPricingListId)
    {
        $supplierPricingList = $this->supplierPricingListFactory->create();
        $this->resourceSupplierPricingList->load($supplierPricingList, $supplierPricingListId);
        if (!$supplierPricingList->getId()) {
            throw new NoSuchEntityException(__('Supplier Pricelist with id "%1" does not exist.', $supplierPricingListId));
        }
        return $supplierPricingList;
    }

    /**
     * Delete supplier pricelist.
     *
     * @param \Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface $supplierPricingList
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface $supplierPricingList)
    {
        try {
            $this->resourceSupplierPricingList->delete($supplierPricingList);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete supplier pricelist by ID.
     *
     * @param int $supplierPricingListId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($supplierPricingListId)
    {
        return $this->delete($this->getById($supplierPricingListId));
    }
}
