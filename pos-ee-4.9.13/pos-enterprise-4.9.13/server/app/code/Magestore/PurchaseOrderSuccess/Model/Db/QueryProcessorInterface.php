<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Db;


interface QueryProcessorInterface
{
    
    /**
     * Define query types
     */
    CONST QUERY_TYPE_UPDATE = 'update';
    CONST QUERY_TYPE_INSERT = 'insert';
    CONST QUERY_TYPE_DELETE = 'delete';
    
    /**
     * Add query to processor
     * 
     * @param array $queryData
     * @param string $process
     * @return \Magestore\PurchaseOrderSuccess\Model\Db\QueryProcessorInterface
     */
    public function addQuery($queryData, $process = null);
    
    /**
     * Get queries in queue
     * 
     * @param string $process
     * @return array
     */
    public function getQueryQueue($process = null);
    
    /**
     * Process queries in queue
     * 
     * @param string $process
     * @return \Magestore\PurchaseOrderSuccess\Model\Db\QueryProcessorInterface
     */
    public function process($process = null);
    
    /**
     * Remove queries in the queue
     * 
     * @param string $process
     * @return \Magestore\PurchaseOrderSuccess\Model\Db\QueryProcessorInterface
     */
    public function resetQueue($process = null);
    
    /**
     * Start processing
     * 
     * @param string $process
     * @return \Magestore\PurchaseOrderSuccess\Model\Db\QueryProcessorInterface
     */
    public function start($process = null);
    
}