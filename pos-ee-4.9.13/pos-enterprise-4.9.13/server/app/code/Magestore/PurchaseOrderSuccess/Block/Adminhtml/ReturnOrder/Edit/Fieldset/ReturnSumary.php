<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Fieldset;

/**
 * Class ReturnSumary
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Fieldset
 */
class ReturnSumary extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magestore_PurchaseOrderSuccess::grid/return_container.phtml';

    /**
     * @var \Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSumary\Item
     */
    protected $blockGrid;

    /**
     * @var string
     */
    protected $dataFormPart = 'os_return_order_form';

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
                'Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSumary\Item',
                'returnorder.item.grid'
            );
        }
        return $this->blockGrid;
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
     * @return string
     */
    public function getDataFormPart(){
        return $this->dataFormPart;
    }
}