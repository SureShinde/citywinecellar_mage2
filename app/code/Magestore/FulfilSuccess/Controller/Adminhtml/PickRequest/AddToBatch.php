<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest;

use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;
use Magestore\FulfilSuccess\Api\BatchRepositoryInterface;
use Magestore\FulfilSuccess\Service\PickRequest\BatchService;
use Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest\CollectionFactory;

class AddToBatch extends \Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest
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


    public function __construct(
        Context $context, 
        Filter $filter, 
        CollectionFactory $collectionFactory,
        BatchRepositoryInterface $batchRepository,
        BatchService $batchService
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->batchRepository = $batchRepository;
        $this->batchService = $batchService;
        parent::__construct($context);
    }

    /**
     * Add pick requests to the Batch
     * 
     */
    public function execute()
    {
        $batchId = $this->_request->getParam('batch_id');
        $collection = $this->filter->getCollection($this->collectionFactory->create())
                                ->addFieldToFilter([PickRequestInterface::BATCH_ID, PickRequestInterface::BATCH_ID], 
                                        [['eq' => 0], ['null' => true]]);
        $pickRequestIds = $collection->getAllIds();
        if(count($pickRequestIds)) {
            $batch = $batchId ? $this->batchRepository->getById($batchId) : $this->batchRepository->newBatch();
         
            try{
                $this->batchService->addRequestsToBatch($batch, $pickRequestIds);
                $this->messageManager->addSuccessMessage(__('%1 Picking Request(s) has been added to the batch', count($pickRequestIds)));
            } catch(\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addWarningMessage(__('There is no picking request added to the batch'));
        }
        
        $this->_redirect('*/*/index');
    }
}