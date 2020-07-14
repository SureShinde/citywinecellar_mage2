<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\Package;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Shipment;
use Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface;
use Magestore\FulfilSuccess\Api\OrderItemRepositoryInterface;
use Magestore\FulfilSuccess\Model\ResourceModel\Package\PackageItemFactory;
use Magestore\OrderSuccess\Api\Data\OrderItemInterface as OrderSuccessOrderItemInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Class PackageService
 *
 * @package Magestore\FulfilSuccess\Service\Package
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PackageService
{
    /**
     * @var \Magestore\FulfilSuccess\Model\Package\PackageFactory
     */
    protected $packageFactory;
    /**
     * @var \Magestore\FulfilSuccess\Model\Package\PackageItemFactory
     */
    protected $packageItemFactory;
    /**
     * @var \Magestore\FulfilSuccess\Api\PackageRepositoryInterface
     */
    protected $packageRepository;
    /**
     * @var \Magestore\FulfilSuccess\Api\PackageItemRepositoryInterface
     */
    protected $packageItemRepository;
    /**
     * @var PackageItemFactory
     */
    protected $packageItemResourceFactory;
    /**
     * @var OrderItemRepositoryInterface
     */
    protected $orderFulfilItemRepository;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item
     */
    protected $resourceOrderItem;
    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $orderItemFactory;

    /**
     * PackageService constructor.
     *
     * @param \Magestore\FulfilSuccess\Model\Package\PackageFactory $packageFactory
     * @param \Magestore\FulfilSuccess\Model\Package\PackageItemFactory $packageItemFactory
     * @param \Magestore\FulfilSuccess\Api\PackageRepositoryInterface $packageRepository
     * @param \Magestore\FulfilSuccess\Api\PackageItemRepositoryInterface $packageItemRepository
     * @param PackageItemFactory $packageItemResourceFactory
     * @param OrderItemRepositoryInterface $orderFulfilItemRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\Item $resourceOrderItem
     * @param \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
     */
    public function __construct(
        \Magestore\FulfilSuccess\Model\Package\PackageFactory $packageFactory,
        \Magestore\FulfilSuccess\Model\Package\PackageItemFactory $packageItemFactory,
        \Magestore\FulfilSuccess\Api\PackageRepositoryInterface $packageRepository,
        \Magestore\FulfilSuccess\Api\PackageItemRepositoryInterface $packageItemRepository,
        PackageItemFactory $packageItemResourceFactory,
        OrderItemRepositoryInterface $orderFulfilItemRepository,
        \Magento\Sales\Model\ResourceModel\Order\Item $resourceOrderItem,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
    ) {
        $this->packageFactory = $packageFactory;
        $this->packageItemFactory = $packageItemFactory;
        $this->packageRepository = $packageRepository;
        $this->packageItemRepository = $packageItemRepository;
        $this->packageItemResourceFactory = $packageItemResourceFactory;
        $this->orderFulfilItemRepository = $orderFulfilItemRepository;
        $this->resourceOrderItem = $resourceOrderItem;
        $this->orderItemFactory = $orderItemFactory;
    }

    /**
     * Create packages
     *
     * @param Shipment $shipment
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function createPackages(
        \Magento\Sales\Model\Order\Shipment $shipment,
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest
    ) {
        $packages = $request->getParam('packages');

        foreach ($packages as $package) {
            /** @var \Magestore\FulfilSuccess\Model\Package\Package $packageObj */
            $packageObj = $this->packageFactory->create();
            $packageObj
                ->setPackRequestId($packRequest->getPackRequestId())
                ->setShipmentId($shipment->getEntityId())
                ->setWarehouseId($packRequest->getWarehouseId())
                ->setContainer(isset($package["params"]["container"]) ? $package["params"]["container"] : "")
                ->setCustomValue(isset($package["params"]["customs_value"]) ? $package["params"]["customs_value"] : "")
                ->setWeight(isset($package["params"]["weight"]) ? $package["params"]["weight"] : 0)
                ->setLength(isset($package["params"]["length"]) ? $package["params"]["length"] : 0)
                ->setWidth(isset($package["params"]["width"]) ? $package["params"]["width"] : 0)
                ->setHeight(isset($package["params"]["height"]) ? $package["params"]["height"] : 0)
                ->setWeightUnits(isset($package["params"]["weight_units"]) ? $package["params"]["weight_units"] : "")
                ->setDimensionUnits(
                    isset($package["params"]["dimension_units"]) ? $package["params"]["dimension_units"] : ""
                )
                ->setContentType(isset($package["params"]["content_type"]) ? $package["params"]["content_type"] : "")
                ->setContentTypeOther(
                    isset($package["params"]["content_type_other"]) ? $package["params"]["content_type_other"] : ""
                )
                ->setDeliveryConfirmation(
                    isset($package["params"]["delivery_confirmation"]) ?
                        $package["params"]["delivery_confirmation"] :
                        ""
                )
                ->setImage("")
                ->setSourceCode($packRequest->getSourceCode());
            if ($shipment->getTracksCollection()) {
                $packageObj->setTrackId($shipment->getTracksCollection()->getFirstItem()->getId());
            }
            $this->packageRepository->save($packageObj);

            foreach ($package["items"] as $packageItem) {
                /** @var \Magestore\FulfilSuccess\Model\Package\PackageItem $packageItemObj */
                $packageItemObj = $this->packageItemFactory->create();
                $packageItemObj
                    ->setPackageId($packageObj->getId())
                    ->setQty($packageItem["qty"])
                    ->setCustomsValue($packageItem["customs_value"])
                    ->setPrice($packageItem["price"])
                    ->setName($packageItem["name"])
                    ->setWeight($packageItem["weight"])
                    ->setProductId($packageItem["product_id"])
                    ->setOrderItemId($packageItem["order_item_id"]);

                $this->packageItemRepository->save($packageItemObj);
            }
        }
    }

    /**
     * Process packages
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return array
     */
    public function processPackages(\Magento\Framework\App\RequestInterface $request)
    {
        $result = [];
        $packages = $request->getParam('packages');

        foreach ($packages as $package) {
            foreach ($package["items"] as $packageItem) {
                if (array_key_exists($packageItem["order_item_id"], $result)) {
                    $result[$packageItem["order_item_id"]] += $packageItem["qty"];
                } else {
                    $result[$packageItem["order_item_id"]] = $packageItem["qty"];
                }
            }
        }

        return $result;
    }

    /**
     * Create package by shipment
     *
     * @param Shipment $shipment
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest
     * @param int $trackId
     * @throws LocalizedException
     */
    public function createPackageByShipment(
        \Magento\Sales\Model\Order\Shipment $shipment,
        \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest,
        $trackId = null
    ) {
        try {
            /** @var \Magestore\FulfilSuccess\Model\Package\Package $packageObj */
            $packageObj = $this->packageFactory->create();
            if ($trackId) {
                $packageObj->setTrackId($trackId);
            }
            $shipmentId = $shipment->getId();
            $packageObj->setPackRequestId($packRequest->getPackRequestId());
            $packageObj->setShipmentId($shipmentId);
            $packageObj->setWarehouseId($packRequest->getWarehouseId());
            $packageObj->setSourceCode($packRequest->getSourceCode());
            /** Save package */
            $this->packageRepository->save($packageObj);
            $packageId = $packageObj->getId();
            $itemData = [];
            $items = $shipment->getItems();
            foreach ($items as $item) {
                if (!$item->getId()) {
                    continue;
                }
                $itemData['package_id'] = $packageId;
                $itemData['qty'] = $item->getQty();
                $itemData['price'] = $item->getPrice();
                $itemData['name'] = $item->getName();
                $itemData['weight'] = $item->getWeight() ? $item->getWeight() : '';
                $itemData['product_id'] = $item->getProductId();
                $itemData['order_item_id'] = $item->getOrderItemId();

                /** add items to package */
                $this->addItemToPackage($itemData);
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__('Could not create new package!'));
        }
    }

    /**
     * Add item to package
     *
     * @param array $itemData ['package_id', 'qty', 'price', 'name', 'weight', 'product_id', 'order_item_id']
     */
    public function addItemToPackage(array $itemData)
    {
        /** @var \Magestore\FulfilSuccess\Model\ResourceModel\Package\PackageItem $packageItemResource */
        $packageItemResource = $this->packageItemResourceFactory->create();
        $packageItemResource->addItems($itemData);
    }

    /**
     * Update qty prepare ship
     *
     * @param Shipment $shipment
     */
    public function updatePrepareShipQty(Shipment $shipment)
    {
        $moveItems = [];
        /** @var \Magento\Sales\Api\Data\ShipmentItemInterface[] $items */
        $items = $shipment->getItems();
        /** @var \Magento\Sales\Api\Data\ShipmentItemInterface  $item */
        foreach ($items as $item) {
            $qty = $item->getQty();
            if (!$qty) {
                $orderItem = $this->orderItemFactory->create();
                $this->resourceOrderItem->load($orderItem, $item->getOrderItemId());
                if ($orderItem->getParentItemId()) {
                    $parentItem = $this->orderItemFactory->create();
                    $this->resourceOrderItem->load($parentItem, $orderItem->getParentItemId());
                    if ($parentItem->getProductType() == Configurable::TYPE_CODE) {
                        $qty = (
                            isset($moveItems[$orderItem->getParentItemId()]) &&
                            ($moveItems[$orderItem->getParentItemId()]['qty'] != 0)
                        )
                            ? $moveItems[$orderItem->getParentItemId()]['qty']
                            : 0;
                    }
                }
            }
            $moveItems[$item->getOrderItemId()] = [
                PackRequestItemInterface::ITEM_ID => $item->getOrderItemId(),
                'qty' => $qty,
                OrderSuccessOrderItemInterface::QTY_PREPARESHIP => -$qty,
            ];
        }
        if (count($moveItems)) {
            /* update pack_qty of items in Sales Sales */
            $this->orderFulfilItemRepository->massUpdatePrepareShipQty($moveItems);
        }
    }
}
