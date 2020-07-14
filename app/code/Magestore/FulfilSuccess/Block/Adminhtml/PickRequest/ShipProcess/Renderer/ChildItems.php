<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Block\Adminhtml\PickRequest\ShipProcess\Renderer;

class ChildItems extends \Magento\Backend\Block\Template
{
    
    /**
     * @var string
     */
    protected $_template = 'pickRequest/shipprocess/renderer/child_items.phtml';
    
    /**
     * @var \Magento\Backend\Block\Template 
     */
    protected $parentBlock;
    
    /**
     * Set parent block
     * 
     * @param \Magento\Backend\Block\Template  $parent
     * @return \Magestore\FulfilSuccess\Block\Adminhtml\PickRequest\ShipProcess\Renderer\ChildItems
     */
    public function setParentBlock($parent)
    {
        $this->parentBlock = $parent;
        return $this;
    }
    
    /**
     * Retrieve parent block
     *
     * @return \Magento\Framework\View\Element\AbstractBlock|bool
     */    
    public function getParentBlock()
    {
        return $this->parentBlock;
    }
    
    /**
     * Get product id of child item
     * 
     * @param \Magento\Sales\Model\Order\Item $item
     * @return int
     */
    public function getSimpleProductId($item)
    {
        return $this->getParentBlock()->getSimpleProductId($item);
    }
    
    /**
     * Get list available warehouses to pick item
     * 
     * @param \Magento\Sales\Model\Order\Item $item
     * @return array
     */
    public function getAvailableWarehouses($item)
    {
        return $this->getParentBlock()->getAvailableWarehouses($item);
    }
    
    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @return mixed|null
     */
    public function getSelectionAttributes($item)
    {
        $options = $item->getProductOptions();
        if (isset($options['bundle_selection_attributes'])) {
            return $options['bundle_selection_attributes'];
        }
        return null;
    }   
    
    /**
     * 
     * @param \Magento\Framework\DataObject $item
     * @return float
     */
    public function getBundleQty($item)
    {
        $attributes = $this->getSelectionAttributes($item);
        if(isset($attributes['qty'])) {
            return $attributes['qty'];
        }
        return 0;
    }    
    
}