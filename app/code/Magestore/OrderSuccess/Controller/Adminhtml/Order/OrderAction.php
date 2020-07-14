<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order;

use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magestore\OrderSuccess\Api\BatchRepositoryInterface;
use Magestore\OrderSuccess\Service\BatchService;
use Magestore\OrderSuccess\Service\TagService;
use Magestore\OrderSuccess\Service\ShipService;
use Magestore\OrderSuccess\Service\OrderService;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\OrderManagementInterface;

/**
 * Class OrderAction
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Sales
 */
abstract class OrderAction extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var BatchRepositoryInterface 
     */
    protected $batchRepository;    
    
    /**
     * @var BatchService 
     */
    protected $batchService;

    /**
     * @var TagService
     */
    protected $tagService;
    
    /**
     * @var ShipService
     */
    protected $shipService;
    
    /**
     * @var OrderService 
     */
    protected $orderService;

    /** @var JsonFactory  */
    protected $jsonFactory;
    
    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;


    /**
     * OrderAction constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param BatchRepositoryInterface $batchRepository
     * @param BatchService $batchService
     * @param TagService $tagService
     * @param OrderService $orderService
     * @param ShipService $shipService
     * @param JsonFactory $jsonFactory
     * @param OrderManagementInterface $orderManagement
     */
    public function __construct(
        Context $context, 
        Filter $filter, 
        CollectionFactory $collectionFactory,
        BatchRepositoryInterface $batchRepository,
        BatchService $batchService,
        TagService $tagService,
        OrderService $orderService,
        ShipService $shipService,
        JsonFactory $jsonFactory,
        OrderManagementInterface $orderManagement
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->batchRepository = $batchRepository;
        $this->batchService = $batchService;
        $this->tagService = $tagService;
        $this->orderService = $orderService;
        $this->shipService = $shipService;
        $this->jsonFactory = $jsonFactory;
        $this->orderManagement = $orderManagement;
        parent::__construct($context);
    }

    /**
     * Return component referer url
     *
     * @return null|string
     */
    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl() ? : 'ordersuccess/needverify/index';
    }

    /**
     * Batch action
     *
     */
    public function execute()
    {
        return;
    }
}