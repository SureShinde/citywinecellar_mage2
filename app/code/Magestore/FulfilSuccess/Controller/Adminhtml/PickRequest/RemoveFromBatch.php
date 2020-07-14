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

class RemoveFromBatch extends \Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest
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
        if($batchId) {
            $batch = $this->batchRepository->getById($batchId);
            if(!$batch->getId()) {
                 $this->messageManager->addWarningMessage(__('The batch does not exist'));
                 return $this->_redirect('*/*/index');
            }
            $collection = $this->filter->getCollection($this->collectionFactory->create())
                                    ->addFieldToFilter(PickRequestInterface::BATCH_ID, $batchId);
            $pickRequestIds = $collection->getAllIds();
            if(count($pickRequestIds)) {
                $this->batchService->removeRequestsFromBatch($batch, $pickRequestIds);
                $this->messageManager->addSuccessMessage(__('%1 Picking Request(s) has been removed from the batch', count($pickRequestIds)));
            } else {
                $this->messageManager->addWarningMessage(__('No Picking Request has been removed from the batch'));
            }
        } else {
            $this->messageManager->addWarningMessage(__('No batch is selected'));
        }
        
        $this->_redirect('*/*/index');
    }
}