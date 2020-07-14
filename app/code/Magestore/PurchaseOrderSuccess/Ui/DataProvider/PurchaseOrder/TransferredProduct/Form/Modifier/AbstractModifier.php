<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\TransferredProduct\Form\Modifier;

/**
 * Class AbstractModifier
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\TransferredProduct\Form\Modifier
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
    protected $scopeName = 'os_purchase_order_transferred_product_form.os_purchase_order_transferred_product_form';
    
    public function getPurchaseOrderId(){
        if(!$this->purchaseId){
            $this->purchaseId = $this->request->getParam('purchase_id', null);
        }
        return $this->purchaseId;
    }
}