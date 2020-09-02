<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Ui\DataProvider\Tag;

/**
 * Class SetTAgActions
 * @package Magestore\OrderSuccess\Ui\DataProvider\Tag
 */
class SetTagActions extends TagActions
{
    /**
     * @var string
     */
    protected $urlPath = '*/order/addTag';

    /**
     * @var string
     */
    protected $urlRemovePath = '*/order/removeTag';

    /**
     * get actions
     * 
     * @return array
     */
    public function getActions($posistion)
    {
        $actions = [];
        $actions[0] = [
            'type' => 'settag',
            'label' => __('Remove All Tags'),
            'url' => $this->urlBuilder->getUrl($this->urlRemovePath,
                [
                    'order_position' => $posistion
                ]),
        ];

        $actions = array_merge($actions, parent::getActions($posistion));
        return $actions;
    }
}