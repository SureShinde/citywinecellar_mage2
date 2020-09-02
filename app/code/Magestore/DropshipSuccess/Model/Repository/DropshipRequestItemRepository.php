<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Model\Repository;

use Magento\Catalog\Model\Product\Exception;
use Magestore\DropshipSuccess\Api\Data;
use Magestore\DropshipSuccess\Api\DropshipRequestItemRepositoryInterface;
use Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item as ResourceDropshipRequestItem;
use Magestore\DropshipSuccess\Model\DropshipRequest\ItemFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class BlockRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DropshipRequestItemRepository implements DropshipRequestItemRepositoryInterface
{
    /**
     * @var ResourceDropshipRequestItem
     */
    protected $resourceDropshipRequestItem;

    /**
     * @var ItemFactory|DropshipRequestItemFactory
     */
    protected $dropshipRequestItemFactory;

    /**
     * DropshipRequestItemrRepository constructor.
     * @param ResourceDropshipRequestItem $resource
     * @param DropshipRequestItemFactory $dropshipRequestItemFactory
     */
    public function __construct(
        ResourceDropshipRequestItem $resource,
        ItemFactory $dropshipRequestItemFactory
    ) {
        $this->resourceDropshipRequestItem = $resource;
        $this->dropshipRequestItemFactory = $dropshipRequestItemFactory;
    }

    /**
     * Save dropship request item.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipRequestItemInterface $dropshipRequestItem
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipRequestItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\DropshipRequestItemInterface $dropshipRequestItem)
    {
        try {
            $this->resourceDropshipRequestItem->save($dropshipRequestItem);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $dropshipRequestItem;
    }

    /**
     * Retrieve dropship request item.
     *
     * @param int $dropshipRequestItemId
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipRequestItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dropshipRequestItemId)
    {
        $dropshipRequestItem = $this->dropshipRequestItemFactory->create();
        $this->resourceDropshipRequestItem->load($dropshipRequestItem, $dropshipRequestItemId);
        if (!$dropshipRequestItem->getId()) {
            throw new NoSuchEntityException(__('Dropship request item with id "%1" does not exist.', $dropshipRequestItemId));
        }
        return $dropshipRequestItem;
    }

    /**
     * Delete dropship request item.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipRequestItemInterface $dropshipRequestItem
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\DropshipRequestItemInterface $dropshipRequestItem)
    {
        try {
            $this->resourceDropshipRequestItem->delete($dropshipRequestItem);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete dropship request item by ID.
     *
     * @param int $dropshipRequestItemId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($dropshipRequestItemId)
    {
        return $this->delete($this->getById($dropshipRequestItemId));
    }

    /**
     * Cancel dropship request item by ID.
     *
     */
    public function cancelItemById($dropshipRequestItemId)
    {
        $item = $this->getById($dropshipRequestItemId);
        $requestQty = $item->getQtyRequested();
        $shipedQty = $item->getQtyShipped();
        $canceledQty = $requestQty - $shipedQty;
        try{
            $item->setQtyCanceled($canceledQty);
            $this->save($item);
        }catch (Exception $e){

        }
        return $canceledQty;
    }
}
