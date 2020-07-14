<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\Product;

/**
 * Class Save
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\Product
 */
class Grid extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\AbstractAction
{
    const BLOCK_GRID = 'Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSumary\Item';
    const BLOCK_GRID_NAME = 'returnorder.item.grid';

    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    public function execute() {
        $this->initVariable();

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                static::BLOCK_GRID,
                static::BLOCK_GRID_NAME
            )->toHtml()
        );
    }

    private function initVariable() {
        $this->resultRawFactory = $this->_objectManager->get('Magento\Framework\Controller\Result\RawFactory');
        $this->layoutFactory = $this->_objectManager->get('Magento\Framework\View\LayoutFactory');
    }
}