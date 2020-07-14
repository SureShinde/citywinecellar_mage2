<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest;

class Detail extends \Magento\Sales\Block\Adminhtml\Order\View
{
    const POSITION_TOP = 'top';
    const POSITION_LEFT = 'left';
    const POSITION_RIGHT = 'right';
    const POSITION_BOTTOM = 'bottom';
    const POSITION_BOTTOM_LEFT = 'bottom_left';
    const POSITION_BOTTOM_RIGHT = 'bottom_right';

    protected $_childs = array();

    protected function _prepareLayout()
    {
        $this->_prepareChilds();
        $this->setTemplate('Magestore_FulfilSuccess::abstractRequest/detail.phtml');
        parent::_prepareLayout();
    }

    /**
     * @return $this
     */
    protected function _prepareChilds()
    {
        return $this;
    }

    public function _addChild($block, $alias, $position, $priority = 0)
    {
        if ($block) {
            $block = $this->addChild($alias, $block);
            if ($priority) {
                $this->_childs[$position][$priority] = $block;
            } else {
                $this->_childs[$position][] = $block;
            }
        }
        return $this;
    }

    public function addTopChild($block, $alias, $priority = 0)
    {
        return $this->_addChild($block, $alias, self::POSITION_TOP, $priority);
    }

    public function addBottomChild($block, $alias, $priority = 0)
    {
        return $this->_addChild($block, $alias, self::POSITION_BOTTOM, $priority);
    }

    public function addLeftChild($block, $alias, $priority = 0)
    {
        return $this->_addChild($block, $alias, self::POSITION_LEFT, $priority);
    }

    public function addRightChild($block, $alias, $priority = 0)
    {
        return $this->_addChild($block, $alias, self::POSITION_RIGHT, $priority);
    }

    public function addBottomLeftChild($block, $alias, $priority = 0)
    {
        return $this->_addChild($block, $alias, self::POSITION_BOTTOM_LEFT, $priority);
    }

    public function addBottomRightChild($block, $alias, $priority = 0)
    {
        return $this->_addChild($block, $alias, self::POSITION_BOTTOM_RIGHT, $priority);
    }

    public function _getChildsHtml($position)
    {
        if (!isset($this->_childs[$position]) || !count($this->_childs[$position]))
            return null;

        $html = '';
        $beforeHtml = '';
        $afterHtml = '';
        $i = 0;
        foreach ($this->_childs[$position] as $child) {
            $i++;
            if ($position == self::POSITION_TOP) {
                if ($i % 2 == 1) {
                    $beforeHtml = '<div style="width: 48%; float: left;">';
                    $afterHtml = '</div>';
                } else {
                    $beforeHtml = '<div style="width: 48%; float: right;">';
                    $afterHtml = '</div><div style="clear: both; height: 20px;"></div>';
                }

            }
            if (!($position == self::POSITION_BOTTOM_RIGHT && 0)) {
                $html .= $beforeHtml . $child->toHtml() . $afterHtml;
            }
        }

        return $html;
    }

    public function getTopChilds()
    {
        return $this->_getChildsHtml(self::POSITION_TOP);
    }

    public function getBottomChilds()
    {
        return $this->_getChildsHtml(self::POSITION_BOTTOM);
    }

    public function getLeftChilds()
    {
        return $this->_getChildsHtml(self::POSITION_LEFT);
    }

    public function getRightChilds()
    {
        return $this->_getChildsHtml(self::POSITION_RIGHT);
    }

    public function getBottomLeftChilds()
    {
        return $this->_getChildsHtml(self::POSITION_BOTTOM_LEFT);
    }

    public function getBottomRightChilds()
    {
        return $this->_getChildsHtml(self::POSITION_BOTTOM_RIGHT);
    }

    /**
     * Get before items html
     *
     * @return string
     */
    public function getBeforeItemsHtml()
    {

    }

    /**
     * Get items html
     *
     * @return string
     */
    public function getItemsHtml()
    {
        return $this->getChildHtml('picked_items');
    }

    public function getOrderInfoBlock()
    {
        return 'Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\OrderInfo';
    }

    public function getAccountBlock()
    {
        return 'Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\Account';
    }

    public function getBillingAddressBlock()
    {
        return 'Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\BillingAddress';
    }

    public function getShippingAddressBlock()
    {
        return 'Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\ShippingAddress';
    }

    public function getBarcodeBlock()
    {
        return 'Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\Barcode';
    }

    public function getShippingBlock()
    {
        return 'Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\Shipping';
    }
    
    /**
     * 
     * @param int $age
     * @return string
     */
    public function formatAge($age)
    {
        $days = floor($age / (24*3600));
        $hours = floor($age / 3600) % 24;
        $mins = round($age / 60 ) % 60;
        $string = '';
        if($days) {
            $string .= $days . 'd ';
        }
        $string .= $hours . 'h ';
        $string .= $mins . 'm';
        return $string;
    }    
}