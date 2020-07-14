<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api\Data;

interface BatchInterface 
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const BATCH_ID = 'batch_id';    
    const CODE = 'code';    
    const USER_ID = 'user_id'; 
    
    /**
     * orefix code
     */
    const PREFIX = 'BATCH';

    /**
     * get Batch id
     *
     * @return int|null
     */
    public function getId();    
    
    /**
     * get Batch id
     *
     * @return int|null
     */
    public function getBatchId();  
    
    /**
     * get Code of Batch
     *
     * @return string
     */
    public function getCode();    

    /**
     * get User id
     *
     * @return int|null
     */
    public function getUserId();   
    
    /**
     * set Batch ID
     * 
     * @param int $id
     */
    public function setId($id);
    
    /**
     * set Batch ID
     * 
     * @param int $id
     */
    public function setBatchId($id);

    /**
     * set Code
     * 
     * @param string $code
     */
    public function setCode($code);

    /**
     * set User ID
     * 
     * @param int $userId
     */
    public function setUserId($userId);    
      
}