<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Api;


interface SupplierRepositoryInterface
{
    /**
     * Save supplier.
     *
     * @param \Magestore\SupplierSuccess\Api\Data\SupplierInterface $supplier
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\SupplierSuccess\Api\Data\SupplierInterface $supplier);

    /**
     * Retrieve supplier.
     *
     * @param int $supplierId
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($supplierId);

    /**
     * Delete supplier.
     *
     * @param \Magestore\SupplierSuccess\Api\Data\SupplierInterface $supplier
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\SupplierSuccess\Api\Data\SupplierInterface $supplier);

    /**
     * Delete supplier by ID.
     *
     * @param int $supplierId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($supplierId);

    /**
     * Retrieve supplier.
     *
     * @param string $supplierCode
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCode($supplierCode);

    /**
     * Retrieve supplier.
     *
     * @param array $productIds
     * @return \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     *
    */
    public function getSupplierByProductId($productIds);

    /**
     * @return mixed
     */
    public function getAllSupplier();
}