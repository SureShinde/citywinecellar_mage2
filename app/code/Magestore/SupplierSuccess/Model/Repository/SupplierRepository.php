<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Model\Repository;

use Magestore\SupplierSuccess\Api\Data;
use Magestore\SupplierSuccess\Api\SupplierRepositoryInterface;
use Magestore\SupplierSuccess\Model\ResourceModel\Supplier as ResourceSupplier;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class BlockRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SupplierRepository implements SupplierRepositoryInterface
{
    /**
     * @var ResourceBlock|ResourceSupplier
     */
    protected $_resourceSupplier;

    /**
     * @var SupplierFactory|\Magestore\SupplierSuccess\Model\SupplierFactory
     */
    protected $_supplierFactory;

    /**
     * @var ResourceSupplier\CollectionFactory
     */
    protected $supplierCollectionFactory;

    /**
     * SupplierRepository constructor.
     * @param ResourceSupplier $resource
     * @param \Magestore\SupplierSuccess\Model\SupplierFactory $supplierFactory
     * @param ResourceSupplier\CollectionFactory $supplierCollectionFactory
     */
    public function __construct(
        ResourceSupplier $resource,
        \Magestore\SupplierSuccess\Model\SupplierFactory $supplierFactory,
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory $supplierCollectionFactory
    ) {
        $this->_resourceSupplier = $resource;
        $this->_supplierFactory = $supplierFactory;
        $this->supplierCollectionFactory = $supplierCollectionFactory;
    }

    /**
     * Save Supplier data
     *
     * @param \Magestore\SupplierSuccess\Api\Data\SupplierInterface $supplier
     * @return Supplier
     * @throws CouldNotSaveException
     */
    public function save(Data\SupplierInterface $supplier)
    {
        try {
            $this->_resourceSupplier->save($supplier);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $supplier;
    }

    /**
     * Load Supplier data by given Supplier Identity
     *
     * @param string $supplierId
     * @return Supplier
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($supplierId)
    {
        $supplier = $this->_supplierFactory->create();
        $this->_resourceSupplier->load($supplier, $supplierId);
        if (!$supplier->getId()) {
            throw new NoSuchEntityException(__('Supplier with id "%1" does not exist.', $supplierId));
        }
        return $supplier;
    }

    /**
     * Delete Supplier
     *
     * @param \Magestore\SupplierSuccess\Api\Data\SupplierInterface $supplier
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\SupplierInterface $supplier)
    {
        try {
            $this->_resourceSupplier->delete($supplier);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Supplier by given Supplier Identity
     *
     * @param string $supplierId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($supplierId)
    {
        return $this->delete($this->getById($supplierId));
    }

    /**
     * Retrieve supplier.
     *
     * @param string $supplierCode
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCode($supplierCode)
    {
        $supplier = $this->_supplierFactory->create();
        $this->_resourceSupplier->load($supplier, $supplierCode, \Magestore\SupplierSuccess\Api\Data\SupplierInterface::SUPPLIER_CODE);
        return $supplier;
    }

    /**
     * Retrieve supplier.
     *
     * @param array $productIds
     * @return \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     */
    public function getSupplierByProductId($productIds)
    {
        /** @var \Magestore\SupplierSuccess\Service\SupplierService $supplierService */
        $supplierService = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magestore\SupplierSuccess\Service\SupplierService'
        );
        $supplier = $supplierService->getSupplierByProductId($productIds);
        return $supplier;
    }

    /**
     * @return mixed
     */
    public function getAllSupplier()
    {
        return $this->supplierCollectionFactory->create();
    }

}
