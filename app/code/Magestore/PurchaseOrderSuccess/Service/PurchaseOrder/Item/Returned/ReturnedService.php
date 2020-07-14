<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\Returned;

use Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemReturnedRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item\ReturnedFactory;

/**
 * Class ReturnedService
 * @package Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\Returned
 */
class ReturnedService 
{
    /**
     * @var PurchaseOrderItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var PurchaseOrderItemReturnedRepositoryInterface
     */
    protected $returnedRepository;

    /**
     * @var ReturnedFactory
     */
    protected $returnedFactory;

    /**
     * @var \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory
     */
    protected $stockItemFactory;

    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\Item
     */
    protected $stockItemResource;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * ReturnedService constructor.
     * @param PurchaseOrderItemRepositoryInterface $itemRepository
     * @param PurchaseOrderItemReturnedRepositoryInterface $returnedRepository
     * @param ReturnedFactory $returnedFactory
     */
    public function __construct(
        PurchaseOrderItemRepositoryInterface $itemRepository,
        PurchaseOrderItemReturnedRepositoryInterface $returnedRepository,
        ReturnedFactory $returnedFactory,
        \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemFactory,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Item $stockItemResource,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
    ){
        $this->itemRepository = $itemRepository;
        $this->returnedRepository = $returnedRepository;
        $this->returnedFactory = $returnedFactory;
        $this->stockItemFactory = $stockItemFactory;
        $this->stockItemResource = $stockItemResource;
        $this->stockItemRepository = $stockItemRepository;
    }
    
    /**
     * @param array $params
     * @return array
     */
    public function processReturnedData($params = []){
        $result = [];
        foreach ($params as $item){
            $result[$item['id']] = $item['returned_qty'];
        }
        return $result;
    }

    /**
     * @param PurchaseOrderItemInterface $purchaseItem
     * @param null $returnedQty
     * @return float|null
     */
    public function getQtyReturned(PurchaseOrderItemInterface $purchaseItem, $returnedQty = null){
        $qty = $purchaseItem->getQtyReceived() - $purchaseItem->getQtyReturned() - $purchaseItem->getQtyTransferred();
        if(!$returnedQty || $returnedQty > $qty)
            $returnedQty = $qty;
        return $returnedQty;
    }

    /**
     * Prepare returned item
     * 
     * @param PurchaseOrderItemInterface $purchaseItem
     * @param float|null $returnedQty
     * @param string|null $returnedTime
     * @param string|null $createdBy
     * @return \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item\Returned
     */
    public function prepareItemReturned(
        PurchaseOrderItemInterface $purchaseItem, $returnedQty = null, $returnedTime = null, $createdBy = null
    ){
        $returnedQty = $this->getQtyReturned($purchaseItem, $returnedQty);
        return $this->returnedFactory->create()
            ->setPurchaseOrderItemId($purchaseItem->getPurchaseOrderItemId())
            ->setQtyReturned($returnedQty)
            ->setReturnedAt($returnedTime)
            ->setCreatedBy($createdBy);
    }

    /**
     * Return an purchase order item by purchase item and qty
     *
     * @param PurchaseOrderInterface $purchaseOrder
     * @param PurchaseOrderItemInterface $purchaseItem
     * @param null $returnedQty
     * @param null $returnedTime
     * @param null $createdBy
     * @param bool $updateStock
     * @return bool
     */
    public function returnItem(
        PurchaseOrderInterface $purchaseOrder, PurchaseOrderItemInterface $purchaseItem, 
        $returnedQty = null, $returnedTime = null, $createdBy = null, $updateStock = false
    ){
        $returnedQty = $this->getQtyReturned($purchaseItem, $returnedQty);
        if($returnedQty == 0)
            return true;
        $itemReturned = $this->prepareItemReturned($purchaseItem, $returnedQty, $returnedTime, $createdBy);
        try{
            $this->returnedRepository->save($itemReturned);
            $purchaseItem->setQtyReturned($purchaseItem->getQtyReturned()+$returnedQty);
            $this->itemRepository->save($purchaseItem);
            $purchaseOrder->setTotalQtyReturned($purchaseOrder->getTotalQtyReturned()+$returnedQty);
            if ($updateStock) {
                /** TODO: Update function subtract source qty if it's needed */
            }
        }catch (\Exception $e){
            return false;
        }
        return true;    
    }
}