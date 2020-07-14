<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Repository\Package;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magestore\FulfilSuccess\Api\PackageRepositoryInterface;

class PackageRepository implements PackageRepositoryInterface
{
    /**
     * @var \Magestore\FulfilSuccess\Model\Package\PackageFactory
     */
    protected $packageFactory;

    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package
     */
    protected $packageResource;

    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\CollectionFactory
     */
    protected $packageCollectionFactory;

    /**
     * PackageRepository constructor.
     * @param \Magestore\FulfilSuccess\Model\Package\PackageFactory $packageFactory
     * @param \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package $packageResource
     * @param \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\CollectionFactory $packageCollectionFactory
     */
    public function __construct(
        \Magestore\FulfilSuccess\Model\Package\PackageFactory $packageFactory,
        \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package $packageResource,
        \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\CollectionFactory $packageCollectionFactory
    )
    {
        $this->packageFactory = $packageFactory;
        $this->packageResource = $packageResource;
        $this->packageCollectionFactory = $packageCollectionFactory;
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
    public function get($id)
    {
        /** @var \Magestore\FulfilSuccess\Model\Package\Package $package */
        $package = $this->packageFactory->create();
        $this->packageResource->load($package, $id);
        if (!$package->getPackageId()) {
            throw new NoSuchEntityException(__('The package with ID "%1" does not exist.', $id));
        }
        return $package;
    }

    /**
     * @inheritDoc
     */
    public function getByTrackingNumber($trackingNumber)
    {
        /** @var \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\Collection $packageCollection */
        $packageCollection = $this->packageCollectionFactory->create();
        $packageCollection->getSelect()->join(
            ['sales_shipment_track' => $packageCollection->getTable('sales_shipment_track')],
            'main_table.track_id = sales_shipment_track.entity_id',
            [
                'tracking_number' => 'sales_shipment_track.track_number'
            ]
        );

        $packageCollection->addFieldToFilter('track_number', $trackingNumber);
        $package = $packageCollection->setPageSize(1)->setCurPage(1)->getFirstItem();

        if (!$package->getPackageId()) {
            throw new NoSuchEntityException(__('The Package with tracking number "%1" does not exist.', $trackingNumber));
        }
        return $package;
    }


    /**
     * @inheritDoc
     */
    public function delete(\Magestore\FulfilSuccess\Api\Data\PackageInterface $package)
    {
        try {
            $this->packageResource->delete($package);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function save(\Magestore\FulfilSuccess\Api\Data\PackageInterface $package)
    {
        try {
            $this->packageResource->save($package);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $package;
    }

}