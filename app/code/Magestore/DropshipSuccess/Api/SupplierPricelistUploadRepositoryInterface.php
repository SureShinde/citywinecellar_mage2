<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api;

/**
 * Interface DropshipShipmentRepositoryInterface
 * @package Magestore\DropshipSuccess\Api
 */
interface SupplierPricelistUploadRepositoryInterface
{
    /**
     * Save supplier pricelist upload
     *
     * @param \Magestore\DropshipSuccess\Api\Data\SupplierPricelistUploadInterface $supplierPricelistUpload
     * @return \Magestore\DropshipSuccess\Api\Data\SupplierPricelistUploadInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\DropshipSuccess\Api\Data\SupplierPricelistUploadInterface $supplierPricelistUpload);
}