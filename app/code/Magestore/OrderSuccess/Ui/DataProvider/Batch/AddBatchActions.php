<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Ui\DataProvider\Batch;

/**
 * Class AddBatchActions
 * @package Magestore\OrderSuccess\Ui\DataProvider\Batch
 */
class AddBatchActions extends BatchActions
{
    /**
     * @var string
     */
    protected $urlPath = '*/order/addToBatch';

    /**
     * @var string
     */
    protected $urlRemovePath = '*/order/removeFromBatch';

    /**
     * @var string
     */
    protected $urlCancelPath = '*/order/cancelBatch';

    /**
     * get actions
     * 
     * @return array
     */
    public function getActions($posistion)
    {
        $actions = [];
        $actions[] = [
            'type' => 'removebatch',
            'label' => __('Remove from Batch'),
            'url' => $this->urlBuilder->getUrl($this->urlRemovePath,
                [
                    'order_position' => $posistion
                ]),
        ];
//        $actions[] = [
//            'type' => 'cancelbatch',
//            'label' => __('Cancel Batch'),
//            'url' => $this->urlBuilder->getUrl($this->urlCancelPath,
//                [
//                    'order_position' => $posistion
//                ]),
//        ];
        $actions[] = [
            'type' => 'newbatch',
            'label' => __('Add to New Batch'),
            'url' => $this->urlBuilder->getUrl($this->urlPath,
                [
                    'order_position' => $posistion
                ]),
        ];
        $actions = array_merge($actions, parent::getActions($posistion));
        return $actions;
    }
}