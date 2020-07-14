<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Grid;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type as PurchaseOrderType;

class Quotation extends PurchaseOrder
{
    public function getFilterType(){
        return PurchaseOrderType::TYPE_QUOTATION;
    }
}
