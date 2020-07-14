<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\Transferred;

use Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\ReturnOrderItemRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\ReturnOrderItemTransferredRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface;
use Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Item\TransferredFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySourceDeductionApi\Model\GetSourceItemBySourceCodeAndSku;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;

/**
 * Class TransferredService
 * @package Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\Transferred
 */
class TransferredService
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ReturnOrderRepositoryInterface
     */
    protected $returnOrderRepository;

    /**
     * @var ReturnOrderItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var ReturnOrderItemTransferredRepositoryInterface
     */
    protected $transferredRepository;

    /**
     * @var TransferredFactory
     */
    protected $transferredFactory;
    /**
     * @var GetSourceItemBySourceCodeAndSku
     */
    private $getSourceItemBySourceCodeAndSku;
    /**
     * @var SourceItemsSaveInterface
     */
    private $sourceItemsSave;

    /**
     * TransferredService constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param ReturnOrderRepositoryInterface $returnOrderRepository
     * @param ReturnOrderItemRepositoryInterface $itemRepository
     * @param ReturnOrderItemTransferredRepositoryInterface $transferredRepository
     * @param TransferredFactory $transferredFactory
     * @param GetSourceItemBySourceCodeAndSku $getSourceItemBySourceCodeAndSku
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ReturnOrderRepositoryInterface $returnOrderRepository,
        ReturnOrderItemRepositoryInterface $itemRepository,
        ReturnOrderItemTransferredRepositoryInterface $transferredRepository,
        TransferredFactory $transferredFactory,
        GetSourceItemBySourceCodeAndSku $getSourceItemBySourceCodeAndSku,
        SourceItemsSaveInterface $sourceItemsSave
    ){
        $this->objectManager = $objectManager;
        $this->returnOrderRepository = $returnOrderRepository;
        $this->itemRepository = $itemRepository;
        $this->transferredRepository = $transferredRepository;
        $this->transferredFactory = $transferredFactory;
        $this->getSourceItemBySourceCodeAndSku = $getSourceItemBySourceCodeAndSku;
        $this->sourceItemsSave = $sourceItemsSave;
    }

    /**
     * @param array $params
     * @return array
     */
    public function processTransferredData($params = []){
        $result = [];
        foreach ($params as $item){
            if(isset($item['transferred_qty']) &&  $item['transferred_qty'] > 0)
                $result[$item['id']] = $item;
        }
        return $result;
    }

    /**
     * @param ReturnOrderItemInterface $returnItem
     * @param null $returnedQty
     * @return float|null
     */
    public function setQtyTransferred(ReturnOrderItemInterface $returnItem, $transferData = []){
        $qty = $returnItem->getQtyReturned() - $returnItem->getQtyTransferred();
        if(!isset($transferData['transferred_qty']) || $transferData['transferred_qty'] > $qty)
            $transferData['transferred_qty'] = $qty;
        return $transferData;
    }

    /**
     * @param ReturnOrderItemInterface $returnItem
     * @param array $transferData
     * @param array $params
     * @param null $createdBy
     * @return \Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Item\Transferred
     */
    public function prepareItemTransferred(
        ReturnOrderItemInterface $returnItem, $transferData = [], $params = [], $createdBy = null
    ){
        $transferData = $this->setQtyTransferred($returnItem, $transferData);
        return $this->transferredFactory->create()
            ->setReturnItemId($returnItem->getReturnItemId())
            ->setQtyTransferred($transferData['transferred_qty'])
//            ->setWarehouseId($params['warehouse_id'])
            ->setTransferredAt($params['transferred_at'])
            ->setCreatedBy($createdBy);
    }

    /**
     * @param ReturnOrderInterface $returnOrder
     * @param ReturnOrderItemInterface $returnItem
     * @param null $transferData
     * @param array $params
     * @param null $createdBy
     * @return bool|\Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Item\Transferred
     */
    public function transferItem(
        ReturnOrderInterface $returnOrder, ReturnOrderItemInterface $returnItem, $transferData = null, $params = [], $createdBy = null
    ){
        $transferData = $this->setQtyTransferred($returnItem, $transferData);
        $itemTransferred = $this->prepareItemTransferred($returnItem, $transferData, $params, $createdBy);
        try{
            $this->transferredRepository->save($itemTransferred);
            $returnItem->setQtyTransferred($returnItem->getQtyTransferred()+$transferData['transferred_qty']);
            $this->itemRepository->save($returnItem);
            $returnOrder->setTotalQtyTransferred($returnOrder->getTotalQtyTransferred()+$transferData['transferred_qty']);
        }catch (\Exception $e){
            return false;
        }
        return $transferData;
    }

    /**
     * @param array $transferredItems
     * @param array $params
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function subtractStockFromCatalog($transferredItems, $params) {
        $sourceItems = [];
        foreach ($transferredItems as $productSku => $qtyTransferred) {
            if(!$qtyTransferred) {
                continue;
            }

            // get source item of transferred items
            try {
                $sourceItem = $this->getSourceItemBySourceCodeAndSku->execute($params['warehouse_id'], $productSku);
            } catch (NoSuchEntityException $e) {
                throw $e;
            }

            $sourceItem->setQuantity($sourceItem->getQuantity() - $qtyTransferred);
            $sourceItems[] = $sourceItem;
        }

        if (!empty($sourceItems)) {
            $this->sourceItemsSave->execute($sourceItems);
        }
    }
}