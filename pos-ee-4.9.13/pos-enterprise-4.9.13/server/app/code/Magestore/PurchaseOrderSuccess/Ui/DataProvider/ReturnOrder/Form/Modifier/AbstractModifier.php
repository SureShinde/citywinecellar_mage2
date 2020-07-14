<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\Form\Modifier;

/**
 * Class AbstractModifier
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier
 */
class AbstractModifier extends \Magestore\PurchaseOrderSuccess\Ui\DataProvider\Modifier\AbstractModifier
{
    /**
     * @var int $returnId
     */
    protected $returnId;

    /**
     * @var string
     */
    protected $scopeName = 'os_return_order_form.os_return_order_form';

    /**
     * Get current return order
     *
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface
     */
    public function getCurrentReturnOrder(){
        return $this->registry->registry('current_return_order');
    }

    public function getReturnOrderId(){
        if(!$this->returnId){
            $this->returnId = $this->request->getParam('id', null);
        }
        return $this->returnId;
    }
}