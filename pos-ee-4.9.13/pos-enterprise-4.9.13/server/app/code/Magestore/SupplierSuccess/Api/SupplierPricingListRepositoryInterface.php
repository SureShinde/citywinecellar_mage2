<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Api;


interface SupplierPricingListRepositoryInterface
{
    /**
     * Save supplier pricelist.
     *
     * @param \Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface $supplierPricingList
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface $supplierPricingList);

    /**
     * Retrieve supplier pricelist.
     *
     * @param int $supplierPricingListId
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($supplierPricingListId);

    /**
     * Delete supplier pricelist.
     *
     * @param \Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface $supplierPricingList
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface $supplierPricingList);

    /**
     * Delete supplier pricelist by ID.
     *
     * @param int $supplierPricingListId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($supplierPricingListId);
}