<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\DataProvider\PickRequest;

use Magento\Framework\UrlInterface;
use Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\Batch\CollectionFactory;
use Magestore\FulfilSuccess\Api\Data\BatchInterface;
use Magento\Backend\Model\Auth\Session as Authsession;

class BatchActions
{
    
    /**
     * @var UrlInterface 
     */
    protected $urlBuilder;
    
    /**
     * @var CollectionFactory 
     */
    protected $collectionFactory;
    
    /**
     * @var Authsession 
     */
    protected $session;
    
    /**
     * @var string
     */
    protected $urlPath;
    
    /**
     * @var type 
     */
    protected $subActionType;
    
    
    public function __construct(UrlInterface $urlBuilder, CollectionFactory $collectionFactory, Authsession $session)
    {
        $this->urlBuilder = $urlBuilder;
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
    }
    
    /**
     * get actions
     * 
     * @return array
     */
    public function getActions()
    {
        $actions = [];
        $currentUserId = $this->session->getUser()->getId();
        
        $collection = $this->collectionFactory->create()
                            ->addFieldToFilter(BatchInterface::USER_ID, $currentUserId)
                            ->setOrder(BatchInterface::BATCH_ID, 'DESC');
        if($collection->getSize()) {
            $batchs = $collection->getItems();
            foreach($batchs as $batch) {
                $actions[] = [
                        'type' => $this->subActionType . $batch->getCode(),
                        'label' => $batch->getCode(),
                        'url' => $this->urlBuilder->getUrl($this->urlPath, ['batch_id' => $batch->getId()]),
                ];                
            }
        }
        
        return $actions;
    }
}