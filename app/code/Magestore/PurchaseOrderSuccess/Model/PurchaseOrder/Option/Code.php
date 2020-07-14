<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option;

/**
 * Class Code
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option
 */
class Code extends \Magestore\PurchaseOrderSuccess\Model\Option\AbstractOption
{
    /**
     * Purchase order code value
     */
    const PURCHASE_ORDER_CODE_PREFIX = 'PO';
    
    const QUOTATION_CODE_PREFIX = 'QUO';

    const RETURN_ORDER_CODE_PREFIX = 'RE';

}