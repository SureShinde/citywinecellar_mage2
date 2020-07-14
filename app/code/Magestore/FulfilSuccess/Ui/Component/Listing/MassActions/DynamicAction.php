<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\Component\Listing\MassActions;

class DynamicAction extends \Magento\Ui\Component\Action
{

    /**
     * @inheritDoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        if(isset($config['action_resource'])) {
             $this->actions = $config['action_resource']->getActions();
        }
        
        parent::prepare();
        
    }
    
}
