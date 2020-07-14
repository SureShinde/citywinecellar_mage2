<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Repository\Package;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magestore\FulfilSuccess\Api\PackageItemRepositoryInterface;

class PackageItemRepository implements PackageItemRepositoryInterface
{
    /**
     * @var \Magestore\FulfilSuccess\Model\Package\PackageItemFactory
     */
    protected $packageItemFactory;

    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\Package\PackageItem
     */
    protected $packageItemResource;

    /**
     * PackageRepository constructor.
     * @param \Magestore\FulfilSuccess\Model\ResourceModel\Package\PackageItem $packageItemResource
     */
    public function __construct(
        \Magestore\FulfilSuccess\Model\Package\PackageItemFactory $packageItemFactory,
        \Magestore\FulfilSuccess\Model\ResourceModel\Package\PackageItem $packageItemResource
    )
    {
        $this->packageItemFactory = $packageItemFactory;
        $this->packageItemResource = $packageItemResource;
    }

    /**
     * @inheritDoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        // TODO: Implement getList() method.
    }

    /**
     * @inheritDoc
     */
    public function get($packageItemId)
    {
        $packageItem = $this->packageItemFactory->create();
        $this->packageItemResource->load($packageItem, $packageItemId);
        if (!$packageItem->getId()) {
            throw new NoSuchEntityException(__('The Package Item with id "%1" does not exist.', $packageItemId));
        }
        return $packageItem;
    }

    /**
     * @inheritDoc
     */
    public function delete(\Magestore\FulfilSuccess\Api\Data\PackageItemInterface $packageItem)
    {
        try {
            $this->packageItemResource->delete($packageItem);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function save(\Magestore\FulfilSuccess\Api\Data\PackageItemInterface $packageItem)
    {
        try {
            $this->packageItemResource->save($packageItem);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $packageItem;
    }
}