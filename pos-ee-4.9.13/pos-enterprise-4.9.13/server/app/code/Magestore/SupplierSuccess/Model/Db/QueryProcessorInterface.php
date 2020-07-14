<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\Db;

/**
 * Interface QueryProcessorInterface
 *
 * @package Magestore\SupplierSuccess\Model\Db
 */
interface QueryProcessorInterface
{
    
    /**
     * Define query types
     */
    const QUERY_TYPE_UPDATE = 'update';
    const QUERY_TYPE_INSERT = 'insert';
    const QUERY_TYPE_DELETE = 'delete';
    
    /**
     * Add query to processor
     *
     * @param array $queryData
     * @param string $process
     * @return QueryProcessorInterface
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
     * @return QueryProcessorInterface
     */
    public function process($process = null);
    
    /**
     * Remove queries in the queue
     *
     * @param string $process
     * @return QueryProcessorInterface
     */
    public function resetQueue($process = null);
    
    /**
     * Start processing
     *
     * @param string $process
     * @return QueryProcessorInterface
     */
    public function start($process = null);
}
