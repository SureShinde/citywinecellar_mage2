<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\Form\Modifier\ReturnSumary\Modifier;

/**
 * Class AbstractModifier
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\TransferredProduct\Form\Modifier
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
    protected $scopeName = 'os_return_order_scan_product_form.os_return_order_scan_product_form';

    public function getPurchaseOrderId(){
        if(!$this->returnId){
            $this->returnId = $this->request->getParam('return_id', null);
        }
        return $this->returnId;
    }
}