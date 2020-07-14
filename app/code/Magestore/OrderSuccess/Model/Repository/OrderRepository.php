<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Model\Repository;

use Magestore\OrderSuccess\Api\Data\OrderInterface as MagestoreOrderInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magestore\OrderSuccess\Api\Data\BatchInterface;
use Magestore\OrderSuccess\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Metadata;
use Magento\Sales\Api\Data\OrderSearchResultInterfaceFactory as SearchResultFactory;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magestore\OrderSuccess\Model\Db\QueryProcessorInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Framework\Exception\InputException;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterface;
use Magento\Sales\Model\Order\ShippingAssignmentBuilder;
use Magento\Framework\App\ObjectManager;
/**
 * Class OrderRepository
 * @package Magestore\OrderSuccess\Model\Repository
 */
class OrderRepository implements OrderRepositoryInterface
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    protected $resource;
    /**
     * @var \Magestore\OrderSuccess\Model\OrderFactory
     */
    protected $orderFactory;
    /**
     * @var \Magestore\OrderSuccess\Model\Db\QueryProcessorInterface
     */
    protected $queryProcessor;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory = null;

    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var ShippingAssignmentBuilder
     */
    private $shippingAssignmentBuilder;

    /**
     * @var OrderInterface[]
     */
    protected $registry = [];

    /**
     * OrderRepository constructor.
     * @param Metadata $metadata
     * @param SearchResultFactory $searchResultFactory
     * @param OrderResource $resource
     * @param OrderFactory $orderFactory
     * @param QueryProcessorInterface $queryProcessor
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param OrderExtensionFactory|null $orderExtensionFactory
     */
    public function __construct(
        Metadata $metadata,
        SearchResultFactory $searchResultFactory,
        OrderResource $resource,
        OrderInterfaceFactory $orderFactory,
        QueryProcessorInterface $queryProcessor,
        OrderCollectionFactory $orderCollectionFactory,
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory = null
    )
    {
        $this->resource = $resource;
        $this->orderFactory = $orderFactory;
        $this->queryProcessor = $queryProcessor;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->metadata = $metadata;
        $this->searchResultFactory = $searchResultFactory;
        $this->orderExtensionFactory = $orderExtensionFactory ?: ObjectManager::getInstance()
            ->get(\Magento\Sales\Api\Data\OrderExtensionFactory::class);
    }
    /**
     * create a new batch
     *
     * @return BatchInterface
     */
    public function newBatch()
    {
        $batch = $this->batchFactory->create();
        $batch->setUserId($this->session->getUser()->getId());
        return $this->save($batch);
    }
    /**
     * Mass update order
     *
     * @param string $actionKey
     * @param string $actionValue
     * @param array $orderIds
     */
    public function massUpdate($orderIds, $actionKey, $actionValue)
    {
        if (!count($orderIds)) {
            return;
        }
        $this->queryProcessor->start('massUpdateBatch');
        $this->queryProcessor->addQuery([
            'type' => QueryProcessorInterface::QUERY_TYPE_UPDATE,
            'values' => [$actionKey => $actionValue],
            'condition' => [OrderInterface::ENTITY_ID . ' IN (?)' => $orderIds],
            'table' => $this->resource->getMainTable()
        ], 'massUpdateBatch');
        $this->queryProcessor->process('massUpdateBatch');
    }
    /**
     * Mass update batch Id of orders
     *
     * @param array $orderIds
     * @param int $batchId
     */
    public function massUpdateBatch($orderIds, $batchId)
    {
        $this->massUpdate($orderIds, MagestoreOrderInterface::BATCH_ID, $batchId);
    }
    /**
     * Mass veriy orders
     *
     * @param array $orderIds
     * @param boolean $isVerify
     */
    public function massVerify($orderIds, $isVerify)
    {
        $this->massUpdate($orderIds, MagestoreOrderInterface::IS_VERIFIED, $isVerify);
    }
    /**
     * Mass update tag orders
     *
     * @param array $orderIds
     * @param boolean $isVerify
     */
    public function massUpdateTag($orderIds, $tag)
    {
        $this->massUpdate($orderIds, MagestoreOrderInterface::TAG_COLOR, $tag);
    }
    /**
     * Retrieve Sales.
     *
     * @param int $orderId
     * @return \Magestore\OrderSuccess\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($orderId)
    {
        $order = $this->orderFactory->create();
        $this->resource->load($order, $orderId);
        if (!$order->getId()) {
            throw new NoSuchEntityException(__('The order with ID "%1" does not exist.', $orderId));
        }
        return $order;
    }
    /**
     * Retrieve requets in a Batch
     *
     * @param array $batchIds
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function getOrderListFromBatch($batchIds)
    {
        $orders = $this->orderCollectionFactory->create()
            ->addFieldToFilter('batch_id', ['in' => $batchIds]);
        return $orders;
    }

    /**
     * load entity
     *
     * @param int $id
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (!$id) {
            throw new InputException(__('Id required'));
        }
        if (!isset($this->registry[$id])) {
            /** @var OrderInterface $entity */
            $entity = $this->metadata->getNewInstance()->load($id);
            if (!$entity->getEntityId()) {
                throw new NoSuchEntityException(__('Requested entity doesn\'t exist'));
            }
            $this->setShippingAssignments($entity);
            $this->registry[$id] = $entity;
        }
        return $this->registry[$id];
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magento\Sales\Api\Data\OrderSearchResultInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();
//        $this->collectionProcessor->process($searchCriteria, $searchResult);
//        $searchResult->setSearchCriteria($searchCriteria);
//        foreach ($searchResult->getItems() as $order) {
//            $this->setShippingAssignments($order);
//        }
//        return $searchResult;
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $this->addFilterGroupToCollection($filterGroup, $searchResult);
        }

        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders === null) {
            $sortOrders = [];
        }
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $searchResult->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }

        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setCurPage($searchCriteria->getCurrentPage());
        $searchResult->setPageSize($searchCriteria->getPageSize());
        foreach ($searchResult->getItems() as $order) {
            $this->setShippingAssignments($order);
        }
        return $searchResult;
    }

    /**
     * Register entity to delete
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $entity
     * @return bool
     */
    public function delete(\Magento\Sales\Api\Data\OrderInterface $entity)
    {
        $this->metadata->getMapper()->delete($entity);
        unset($this->registry[$entity->getEntityId()]);
        return true;
    }

    /**
     * Delete entity by Id
     *
     * @param int $id
     * @return bool
     */
    public function deleteById($id)
    {
        $entity = $this->get($id);
        return $this->delete($entity);
    }

    /**
     * Perform persist operations for one entity
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $entity
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function save(\Magento\Sales\Api\Data\OrderInterface $entity)
    {
        /** @var  \Magento\Sales\Api\Data\OrderExtensionInterface $extensionAttributes */
        $extensionAttributes = $entity->getExtensionAttributes();
        if ($entity->getIsNotVirtual() && $extensionAttributes && $extensionAttributes->getShippingAssignments()) {
            $shippingAssignments = $extensionAttributes->getShippingAssignments();
            if (!empty($shippingAssignments)) {
                $shipping = array_shift($shippingAssignments)->getShipping();
                $entity->setShippingAddress($shipping->getAddress());
                $entity->setShippingMethod($shipping->getMethod());
            }
        }
        $this->metadata->getMapper()->save($entity);
        $this->registry[$entity->getEntityId()] = $entity;
        return $this->registry[$entity->getEntityId()];
    }

    /**
     * @param OrderInterface $order
     * @return void
     */
    private function setShippingAssignments(OrderInterface $order)
    {
        /** @var OrderExtensionInterface $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        } elseif ($extensionAttributes->getShippingAssignments() !== null) {
            return;
        }
        /** @var ShippingAssignmentInterface $shippingAssignment */
        $shippingAssignments = $this->getShippingAssignmentBuilderDependency();
        $shippingAssignments->setOrderId($order->getEntityId());
        $extensionAttributes->setShippingAssignments($shippingAssignments->create());
        $order->setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get the new ShippingAssignmentBuilder dependency for application code
     *
     * @return ShippingAssignmentBuilder
     * @deprecated 100.0.4
     */
    private function getShippingAssignmentBuilderDependency()
    {
        if (!$this->shippingAssignmentBuilder instanceof ShippingAssignmentBuilder) {
            $this->shippingAssignmentBuilder = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Sales\Model\Order\ShippingAssignmentBuilder::class
            );
        }
        return $this->shippingAssignmentBuilder;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Sales\Api\Data\OrderSearchResultInterface $searchResult
     * @return void
     * @deprecated 100.2.0
     * @throws \Magento\Framework\Exception\InputException
     */
    public function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Sales\Api\Data\OrderSearchResultInterface $searchResult
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $conditions[] = [$condition => $filter->getValue()];
            $fields[] = $filter->getField();
        }
        if ($fields) {
            $searchResult->addFieldToFilter($fields, $conditions);
        }
    }
}