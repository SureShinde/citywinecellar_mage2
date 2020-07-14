<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Ui\Component\Listing\MassActions;

/**
 * Class DynamicAction
 * @package Magestore\OrderSuccess\Ui\Component\Listing\MassActions
 */
class DynamicAction extends \Magento\Ui\Component\Action
{

    /**
     * @inheritDoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        $posistion = isset($config['order_position']) ? $config['order_position'] : 'needverify';
        if(isset($config['action_resource'])) {
             $this->actions = $config['action_resource']->getActions($posistion);
        }
        
        parent::prepare();
        
    }
    
}
