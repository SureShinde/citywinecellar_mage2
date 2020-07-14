<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Ui\DataProvider\Batch;

use Magento\Framework\UrlInterface;
use Magestore\OrderSuccess\Model\ResourceModel\Batch\CollectionFactory;
use Magestore\OrderSuccess\Api\Data\BatchInterface;
use Magento\Backend\Model\Auth\Session as Authsession;

/**
 * Class BatchActions
 * @package Magestore\OrderSuccess\Ui\DataProvider\Batch
 */
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
    protected $urlPath = '*/order/addToBatch';


    /**
     * BatchActions constructor.
     * @param UrlInterface $urlBuilder
     * @param CollectionFactory $collectionFactory
     * @param Authsession $session
     */
    public function __construct(
        UrlInterface $urlBuilder,
        CollectionFactory $collectionFactory,
        Authsession $session
    ){
        $this->urlBuilder = $urlBuilder;
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
    }
    
    /**
     * get actions
     * 
     * @return array
     */
    public function getActions($posistion)
    {
        $actions = [];
        $currentUserId = $this->session->getUser()->getId();
        
        $collection = $this->collectionFactory->create()
                            ->addFieldToFilter(BatchInterface::USER_ID, $currentUserId)
                            ->setOrder(BatchInterface::BATCH_ID, 'ASC');
        if($collection->getSize()) {
            $batchs = $collection->getItems();
            foreach($batchs as $batch) {
                $actions[] = [
                        'type' => $batch->getCode(),
                        'label' => $batch->getCode(),
                        'url' => $this->urlBuilder->getUrl($this->urlPath,
                                                            ['batch_id' => $batch->getId(),
                                                            'order_position' => $posistion
                                                            ]),
                ];                
            }
        }
        return $actions;
    }
}