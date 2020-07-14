<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Model\Repository;

use Magestore\SupplierSuccess\Api\Data;
use Magestore\SupplierSuccess\Api\SupplierProductRepositoryInterface;
use Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product as ResourceSupplierProduct;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class BlockRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SupplierProductRepository implements SupplierProductRepositoryInterface
{
    /**
     * @var ResourceSupplier|ResourceSupplierProduct
     */
    protected $resourceSupplierProduct;

    /**
     * @var \Magestore\SupplierSuccess\Model\Supplier\ProductFactory
     */
    protected $supplierProductFactory;

    /**
     * SupplierRepository constructor.
     * @param ResourceSupplierProduct $resource
     * @param \Magestore\SupplierSuccess\Model\Supplier\ProductFactory $supplierProductFactory
     */
    public function __construct(
        ResourceSupplierProduct $resource,
        \Magestore\SupplierSuccess\Model\Supplier\ProductFactory $supplierProductFactory
    ) {
        $this->resourceSupplierProduct = $resource;
        $this->supplierProductFactory = $supplierProductFactory;
    }

    /**
     * Save supplier product.
     *
     * @param \Magestore\SupplierSuccess\Api\Data\SupplierProductInterface $supplierProduct
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\SupplierSuccess\Api\Data\SupplierProductInterface $supplierProduct)
    {
        try {
            $this->resourceSupplierProduct->save($supplierProduct);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $supplierProduct;
    }

    /**
     * Retrieve supplier product.
     *
     * @param int $supplierProductId
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($supplierProductId)
    {
        $supplierProduct = $this->supplierProductFactory->create();
        $this->resourceSupplierProduct->load($supplierProduct, $supplierProductId);
        if (!$supplierProduct->getId()) {
            throw new NoSuchEntityException(__('Supplier with id "%1" does not exist.', $supplierProductId));
        }
        return $supplierProduct;
    }

    /**
     * Delete supplier product.
     *
     * @param \Magestore\SupplierSuccess\Api\Data\SupplierProductInterface $supplierProduct
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\SupplierSuccess\Api\Data\SupplierProductInterface $supplierProduct)
    {
        try {
            $this->resourceSupplierProduct->delete($supplierProduct);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete supplier product by ID.
     *
     * @param int $supplierProductId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($supplierProductId)
    {
        return $this->delete($this->getById($supplierProductId));
    }
}
