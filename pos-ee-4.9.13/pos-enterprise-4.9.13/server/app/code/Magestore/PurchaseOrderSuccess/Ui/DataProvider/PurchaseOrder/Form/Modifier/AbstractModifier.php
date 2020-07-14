<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier;

/**
 * Class AbstractModifier
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier
 */
class AbstractModifier extends \Magestore\PurchaseOrderSuccess\Ui\DataProvider\Modifier\AbstractModifier
{
    /**
     * @var int $purchaseId
     */
    protected $purchaseId;
    
    /**
     * @var string
     */
    protected $scopeName = 'os_purchase_order_form.os_purchase_order_form';

    /**
     * Get current purchase order
     * 
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     */
    public function getCurrentPurchaseOrder(){
        return $this->registry->registry('current_purchase_order');
    }
    
    public function getPurchaseOrderId(){
        if(!$this->purchaseId){
            $this->purchaseId = $this->request->getParam('id', null);
        }
        return $this->purchaseId;
    }
}