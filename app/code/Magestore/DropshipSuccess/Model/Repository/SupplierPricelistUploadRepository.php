<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Model\Repository;

use Magestore\DropshipSuccess\Api\Data;
use Magestore\DropshipSuccess\Api\SupplierPricelistUploadRepositoryInterface;
use Magestore\DropshipSuccess\Model\ResourceModel\Supplier\PricelistUpload as ResourceDropshipSupplierPricelistUpload;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class BlockRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SupplierPricelistUploadRepository implements SupplierPricelistUploadRepositoryInterface
{

    /**
     * @var ResourceDropshipSupplierShipment|ResourceDropshipSupplierPricelistUpload
     */
    protected $resourceDropshipSupplierPricelistUpload;

    /**
     * SupplierPricelistUploadRepository constructor.
     * @param ResourceDropshipSupplierPricelistUpload $resource
     */
    public function __construct(
        ResourceDropshipSupplierPricelistUpload $resource
    ) {
        $this->resourceDropshipSupplierPricelistUpload = $resource;
    }

    /**
     * Save pricelist upload
     *
     * @param \Magestore\DropshipSuccess\Api\Data\SupplierPricelistUploadInterface $supplierPricelistUpload
     * @return \Magestore\DropshipSuccess\Api\Data\SupplierPricelistUploadInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\SupplierPricelistUploadInterface $supplierPricelistUpload)
    {
        try {
            $this->resourceDropshipSupplierPricelistUpload->save($supplierPricelistUpload);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $supplierPricelistUpload;
    }
}
