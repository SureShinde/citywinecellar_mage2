<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\Edit\Tab\Info;

use Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\CollectionFactory;
use Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface;

/**
 * Class Items
 * @package Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\Edit\Tab\Info
 */
class Items extends \Magento\Sales\Block\Adminhtml\Order\View\Items
{
    /**
     * @var \Magestore\DropshipSuccess\Service\DropshipRequest\DropshipRequestItemService
     */
    protected $dropshipRequestItemService;
    
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $orderItemCollectionFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\Collection
     */
    protected $collection;

    /**
     * Items constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\DropshipSuccess\Service\DropshipRequest\DropshipRequestItemService $dropshipRequestItemService
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magestore\DropshipSuccess\Service\DropshipRequest\DropshipRequestItemService $dropshipRequestItemService,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        $this->dropshipRequestItemService = $dropshipRequestItemService;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $data);
        $this->collection = $this->getItemsCollection();
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if(!$this->collection->count()){
            return '';
        }
        return parent::toHtml(); 
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        $columns = array_key_exists('columns', $this->_data) ? $this->_data['columns'] : [];
        return $columns;
    }

    /**
     * @param array $filterByTypes
     * @param bool $nonChildrenOnly
     * @return \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\Collection
     */
    public function getItemsCollection($filterByTypes = [], $nonChildrenOnly = false)
    {
        if(!$this->collection) {
            /**
             * @var DropshipRequestInterface $dropship
             */
            $dropship = $this->getDropshipRequest();
            $collection = $this->dropshipRequestItemService
                ->getItemsInDropship($dropship->getId())
                ->getCanShipItem();
            $collection->getSelect()->joinLeft(
                ['order_item' => $collection->getTable('sales_order_item')],
                'main_table.item_id = order_item.item_id',
                [
                    'order_id', 'parent_item_id', 'quote_item_id', 'store_id', 'product_id', 'product_type',
                    'product_options', 'is_virtual', 'sku', 'name', 'qty_ordered'
                ]
            );
            $order = $this->orderRepository->get($dropship->getOrderId());
            foreach ($collection as $item) {
                $item->setOrder($order);
                $orderItemId = $item->getItemId();
                $orderItem = $this->orderItemCollectionFactory->create()
                    ->addFieldToFilter('item_id', $orderItemId)
                    ->getFirstItem();
                $item->setOrderItem($orderItem);
            }
            $this->collection = $collection;
        }
        return $this->collection;
    }

    /**
     * @return string
     */
    public function getSaveUrl(){
        return $this->getUrl('dropshipsuccess/dropshiprequest_shipment/save', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getBackUrl(){
        $dropshipRequestId = $this->getDropshipRequest()->getId();
        return $this->getUrl('dropshipsuccess/dropshiprequest_shipment/backtofulfil',
                                    [
                                        '_current' => true,
                                        'dropship_request_id' => $dropshipRequestId
                                    ]);
    }

    /**
     * @return mixed
     */
    public function getDropshipRequest(){
        return $this->_coreRegistry->registry(DropshipRequestInterface::CURRENT_DROPSHIP_REQUEST);
    }
}
