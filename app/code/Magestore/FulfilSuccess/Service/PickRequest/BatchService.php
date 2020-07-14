<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\PickRequest;

use Magestore\FulfilSuccess\Api\Data\BatchInterface;

class BatchService
{
    /**
     * @var \Magestore\FulfilSuccess\Api\BatchRepositoryInterface 
     */
    protected $batchRepository;
    
    /**
     * @var \Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface
     */
    protected $pickRequestRepository;
    
    public function __construct(
        \Magestore\FulfilSuccess\Api\BatchRepositoryInterface $batchRepository,
        \Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface $pickRequestRepository
        )
    {
        $this->batchRepository = $batchRepository;
        $this->pickRequestRepository = $pickRequestRepository;
    } 
    
    /**
     * Add PickRequest to Batch
     * 
     * @param BatchInterface $batch
     * @param int $pickRequestId
     */
    public function addRequestToBatch(BatchInterface $batch, $pickRequestId)
    {
        $pickRequest = $this->pickRequestRepository->getById($pickRequestId);
        if($pickRequest->getId()) {
            $pickRequest->setBatchId($batch->getId());
            $this->pickRequestRepository->save($pickRequest);
        }
        return $this;
    }
    
    /**
     * Add PickRequests to Batch
     * 
     * @param BatchInterface $batch
     * @param array $pickRequestIds
     */
    public function addRequestsToBatch(BatchInterface $batch, $pickRequestIds)
    {   
        $this->pickRequestRepository->massUpdateBatch($pickRequestIds, $batch->getId());
        return $this;
    }
    
    /**
     * Remove PickRequest from Batch
     * 
     * @param BatchInterface $batch
     * @param int $pickRequestId
     */
    public function removeRequestFromBatch(BatchInterface $batch, $pickRequestId)
    {
        $pickRequest = $this->pickRequestRepository->getById($pickRequestId);
        if($pickRequest->getId()) {
            $pickRequest->setBatchId(null);
            $this->pickRequestRepository->save($pickRequest);
        }        
        return $this;
    }
    
    /**
     * Remove PickRequest from Batch
     * 
     * @param BatchInterface $batch
     * @param array $pickRequestIds
     */
    public function removeRequestsFromBatch(BatchInterface $batch, $pickRequestIds)
    {
        $this->pickRequestRepository->massUpdateBatch($pickRequestIds, null);
        return $this;
    }

    /**
     * Cancel Batch
     *
     * @param array $orderId
     */
    public function cancelBatch($batchId)
    {
        $pickRequests = $this->pickRequestRepository->getPickRequestFromBatch($batchId);
        $pickRequestIds = $pickRequests->getAllIds();
        $this->pickRequestRepository->massUpdateBatch($pickRequestIds, null);
        $this->batchRepository->deleteById($batchId);
        return $this;
    }
}