<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset;

/**
 * Class PurchaseSumary
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset
 */
class PurchaseSumary extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magestore_PurchaseOrderSuccess::grid/container.phtml';

    /**
     * @var \Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary\Item
     */
    protected $blockGrid;
    
    /**
     * @var \Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary\Total
     */
    protected $blockTotal;

    /**
     * @var string
     */
    protected $dataFormPart = 'os_purchase_order_form';

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary\Item',
                'purchaseorder.item.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockTotal()
    {
        if (null === $this->blockTotal) {
            $this->blockTotal = $this->getLayout()->createBlock(
                'Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary\Total',
                'purchaseorder.total'
            );
        }
        return $this->blockTotal;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * Return HTML of total block
     *
     * @return string
     */
    public function getTotalHtml()
    {
        return $this->getBlockTotal()->toHtml();
    }

    /**
     * @return string
     */
    public function getDataFormPart(){
        return $this->dataFormPart;
    }
}