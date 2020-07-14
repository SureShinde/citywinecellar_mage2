<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\Transferred;

use Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemTransferredRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item\TransferredFactory;
use Magento\InventorySourceDeductionApi\Model\GetSourceItemBySourceCodeAndSku;
use Magestore\PurchaseOrderSuccess\Api\MultiSourceInventory\StockManagementInterface;
use Magestore\PurchaseOrderSuccess\Api\MultiSourceInventory\SourceManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;

/**
 * Class TransferredService
 * @package Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\Transferred
 */
class TransferredService 
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    
    /**
     * @var PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;
    
    /**
     * @var PurchaseOrderItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var PurchaseOrderItemReturnedRepositoryInterface
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
     * @var StockManagementInterface
     */
    private $stockManagement;
    /**
     * @var SourceManagementInterface
     */
    private $sourceManagement;
    /**
     * @var SourceItemsSaveInterface
     */
    private $sourceItemsSave;

    /**
     * TransferredService constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param PurchaseOrderRepositoryInterface $purchaseOrderRepository
     * @param PurchaseOrderItemRepositoryInterface $itemRepository
     * @param PurchaseOrderItemTransferredRepositoryInterface $transferredRepository
     * @param TransferredFactory $transferredFactory
     * @param GetSourceItemBySourceCodeAndSku $getSourceItemBySourceCodeAndSku
     * @param StockManagementInterface $stockManagement
     * @param SourceManagementInterface $sourceManagement
     * @param SourceItemsSaveInterface $sourceItemsSave
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        PurchaseOrderItemRepositoryInterface $itemRepository,
        PurchaseOrderItemTransferredRepositoryInterface $transferredRepository,
        TransferredFactory $transferredFactory,
        GetSourceItemBySourceCodeAndSku $getSourceItemBySourceCodeAndSku,
        StockManagementInterface $stockManagement,
        SourceManagementInterface $sourceManagement,
        SourceItemsSaveInterface $sourceItemsSave
    ){
        $this->objectManager = $objectManager;
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->itemRepository = $itemRepository;
        $this->transferredRepository = $transferredRepository;
        $this->transferredFactory = $transferredFactory;
        $this->getSourceItemBySourceCodeAndSku = $getSourceItemBySourceCodeAndSku;
        $this->stockManagement = $stockManagement;
        $this->sourceManagement = $sourceManagement;
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
     * @param array $transferredData
     * @param array $params
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function updateStock($transferredData, $params) {
        $sourceItems = [];
        foreach ($transferredData as $data) {
            if(!$data['transferred_qty'] || !$data['product_sku'] || !$params['warehouse_id']) {
                continue;
            }

            // get source item of transferred items
            try {
                $sourceItem = $this->getSourceItemBySourceCodeAndSku->execute($params['warehouse_id'], $data['product_sku']);
            } catch (NoSuchEntityException $e) {
                // add product to source if it doesn't exist in source
                $this->stockManagement->createSourceItem($data['product_sku'], $params['warehouse_id']);

                // get source item again
                $sourceItem = $this->getSourceItemBySourceCodeAndSku->execute($params['warehouse_id'], $data['product_sku']);
            }

            $sourceItem->setQuantity($sourceItem->getQuantity() + $data['transferred_qty']);
            $sourceItems[] = $sourceItem;
        }

        if (!empty($sourceItems)) {
            $this->sourceItemsSave->execute($sourceItems);
        }
    }

    /**
     * @param PurchaseOrderItemInterface $purchaseItem
     * @param null $returnedQty
     * @return float|null
     */
    public function setQtyTransferred(PurchaseOrderItemInterface $purchaseItem, $transferData = []){
        $qty = $purchaseItem->getQtyReceived() - $purchaseItem->getQtyTransferred() - $purchaseItem->getQtyReturned();
        if(!isset($transferData['transferred_qty']) || $transferData['transferred_qty'] > $qty)
            $transferData['transferred_qty'] = $qty;
        return $transferData;
    }

    /**
     * @param PurchaseOrderItemInterface $purchaseItem
     * @param array $transferData
     * @param array $params
     * @param null $createdBy
     * @return \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item\Transferred
     */
    public function prepareItemTransferred(
        PurchaseOrderItemInterface $purchaseItem, $transferData = [], $params = [], $createdBy = null
    ){
        $transferData = $this->setQtyTransferred($purchaseItem, $transferData);
        return $this->transferredFactory->create()
            ->setPurchaseOrderItemId($purchaseItem->getPurchaseOrderItemId())
            ->setQtyTransferred($transferData['transferred_qty'])
            ->setWarehouseId($params['warehouse_id'])
            ->setTransferredAt($params['transferred_at'])
            ->setCreatedBy($createdBy)
            ->setPurchaseOrderItem($purchaseItem);
    }

    /**
     * @param PurchaseOrderInterface $purchaseOrder
     * @param PurchaseOrderItemInterface $purchaseItem
     * @param null $transferData
     * @param array $params
     * @param null $createdBy
     * @return bool|\Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item\Transferred
     */
    public function transferItem(
        PurchaseOrderInterface $purchaseOrder, PurchaseOrderItemInterface $purchaseItem, $transferData = null, $params = [], $createdBy = null
    ){
        $transferData = $this->setQtyTransferred($purchaseItem, $transferData);
        $purchaseItem->setPurchaseOrder($purchaseOrder);
        $itemTransferred = $this->prepareItemTransferred($purchaseItem, $transferData, $params, $createdBy);
        try{
            $this->transferredRepository->save($itemTransferred);
            $purchaseItem->setQtyTransferred($purchaseItem->getQtyTransferred()+$transferData['transferred_qty']);
            $this->itemRepository->save($purchaseItem);
            $purchaseOrder->setTotalQtyTransferred($purchaseOrder->getTotalQtyTransferred()+$transferData['transferred_qty']);
        }catch (\Exception $e){
            return false;
        }
        return $transferData;    
    }
}