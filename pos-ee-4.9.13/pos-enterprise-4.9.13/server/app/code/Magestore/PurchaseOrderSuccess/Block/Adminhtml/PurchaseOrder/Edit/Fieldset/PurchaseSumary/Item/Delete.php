<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary\Item;

use Magento\Framework\DataObject;

/**
 * Class Delete
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary\Item
 */
class Delete extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row) {
        $itemId = $row->getPurchaseOrderItemId();
        $productId = $row->getProductId();
        return '<a class="delete_item" value="'.$itemId.'" product_id="'.$productId.'">'.__('Delete').'</a>';
    }
}
