<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\DataProvider\PickRequest;

class AddBatchActions extends BatchActions
{
    /**
     * @var string
     */
    protected $urlPath = '*/*/addToBatch';

    /**
     * @var type 
     */
    protected $subActionType = 'add';    
    
    /**
     * get actions
     * 
     * @return array
     */
    public function getActions()
    {
        $actions = [];
        $actions[] = [
                'type' => 'newbatch',
                'label' => __('Add to a new batch'),
                'url' => $this->urlBuilder->getUrl('*/*/addToBatch'),
        ];
        
        $actions = array_merge($actions, parent::getActions());
        
        return $actions;
    }
}