<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\Locator;


class BatchService implements BatchServiceInterface
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;
    
    public function __construct(
        \Magento\Backend\Model\Session $session
    )
    {
        $this->session = $session;
    }
    
    /**
     * Get currently Batch Id which user is working on
     * 
     * @return int
     */
    public function getCurrentBatchId()
    {
        return $this->session->getData(self::CURRENT_BATCH_SESSION_ID);
    }
    
}