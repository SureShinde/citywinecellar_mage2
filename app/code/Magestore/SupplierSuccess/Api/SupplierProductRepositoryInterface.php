<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Api;


interface SupplierProductRepositoryInterface
{
    /**
     * Save supplier product.
     *
     * @param \Magestore\SupplierSuccess\Api\Data\SupplierProductInterface $supplierProduct
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\SupplierSuccess\Api\Data\SupplierProductInterface $supplierProduct);

    /**
     * Retrieve supplier product.
     *
     * @param int $supplierProductId
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($supplierProductId);

    /**
     * Delete supplier product.
     *
     * @param \Magestore\SupplierSuccess\Api\Data\SupplierProductInterface $supplierProduct
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\SupplierSuccess\Api\Data\SupplierProductInterface $supplierProduct);

    /**
     * Delete supplier product by ID.
     *
     * @param int $supplierProductId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($supplierProductId);
}