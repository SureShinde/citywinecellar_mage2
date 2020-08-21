<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest;

/**
 * Request Detail
 */
class Detail extends \Magento\Sales\Block\Adminhtml\Order\View
{
    const POSITION_TOP = 'top';
    const POSITION_LEFT = 'left';
    const POSITION_RIGHT = 'right';
    const POSITION_BOTTOM = 'bottom';
    const POSITION_BOTTOM_LEFT = 'bottom_left';
    const POSITION_BOTTOM_RIGHT = 'bottom_right';

    protected $_childs = [];

    /**
     * Prepare Layout
     *
     * @return Detail|void
     */
    protected function _prepareLayout()
    {
        $this->_prepareChilds();
        $this->setTemplate('Magestore_FulfilSuccess::abstractRequest/detail.phtml');
        parent::_prepareLayout();
    }

    /**
     * Prepare Childs
     *
     * @return $this
     */
    protected function _prepareChilds()
    {
        return $this;
    }

    /**
     * Add child
     *
     * @param string $block
     * @param string $alias
     * @param string|int $position
     * @param int $priority
     * @return $this
     */
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

    /**
     * Add top child
     *
     * @param string $block
     * @param string $alias
     * @param int $priority
     * @return $this
     */
    public function addTopChild($block, $alias, $priority = 0)
    {
        return $this->_addChild($block, $alias, self::POSITION_TOP, $priority);
    }

    /**
     * Add bottom child
     *
     * @param string $block
     * @param string $alias
     * @param int $priority
     * @return $this
     */
    public function addBottomChild($block, $alias, $priority = 0)
    {
        return $this->_addChild($block, $alias, self::POSITION_BOTTOM, $priority);
    }

    /**
     * Add left child
     *
     * @param string $block
     * @param string $alias
     * @param int $priority
     * @return $this
     */
    public function addLeftChild($block, $alias, $priority = 0)
    {
        return $this->_addChild($block, $alias, self::POSITION_LEFT, $priority);
    }

    /**
     * Add right child
     *
     * @param string $block
     * @param string $alias
     * @param int $priority
     * @return $this
     */
    public function addRightChild($block, $alias, $priority = 0)
    {
        return $this->_addChild($block, $alias, self::POSITION_RIGHT, $priority);
    }

    /**
     * Add bottom left child
     *
     * @param string $block
     * @param string $alias
     * @param int $priority
     * @return $this
     */
    public function addBottomLeftChild($block, $alias, $priority = 0)
    {
        return $this->_addChild($block, $alias, self::POSITION_BOTTOM_LEFT, $priority);
    }

    /**
     * Add bottom right child
     *
     * @param string $block
     * @param string $alias
     * @param int $priority
     * @return $this
     */
    public function addBottomRightChild($block, $alias, $priority = 0)
    {
        return $this->_addChild($block, $alias, self::POSITION_BOTTOM_RIGHT, $priority);
    }

    /**
     * Get childs html
     *
     * @param string $position
     * @return string|null
     */
    public function _getChildsHtml($position)
    {
        if (!isset($this->_childs[$position]) || !count($this->_childs[$position])) {
            return null;
        }

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

    /**
     * Get top child
     *
     * @return string|null
     */
    public function getTopChilds()
    {
        return $this->_getChildsHtml(self::POSITION_TOP);
    }

    /**
     * Get bottom childs
     *
     * @return string|null
     */
    public function getBottomChilds()
    {
        return $this->_getChildsHtml(self::POSITION_BOTTOM);
    }

    /**
     * Get left childs
     *
     * @return string|null
     */
    public function getLeftChilds()
    {
        return $this->_getChildsHtml(self::POSITION_LEFT);
    }

    /**
     * Get right childs
     *
     * @return string|null
     */
    public function getRightChilds()
    {
        return $this->_getChildsHtml(self::POSITION_RIGHT);
    }

    /**
     * Get bottom left childs
     *
     * @return string|null
     */
    public function getBottomLeftChilds()
    {
        return $this->_getChildsHtml(self::POSITION_BOTTOM_LEFT);
    }

    /**
     * Get bottom right childs
     *
     * @return string|null
     */
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
        return '';
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

    /**
     * Get order info block
     *
     * @return string
     */
    public function getOrderInfoBlock()
    {
        return \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\OrderInfo::class;
    }

    /**
     * Get Account Block
     *
     * @return string
     */
    public function getAccountBlock()
    {
        return \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\Account::class;
    }

    /**
     * Get Billing Address Block
     *
     * @return string
     */
    public function getBillingAddressBlock()
    {
        return \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\BillingAddress::class;
    }

    /**
     * Get Shipping Address Block
     *
     * @return string
     */
    public function getShippingAddressBlock()
    {
        return \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\ShippingAddress::class;
    }

    /**
     * Get Barcode Block
     *
     * @return string
     */
    public function getBarcodeBlock()
    {
        return \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\Barcode::class;
    }

    /**
     * Get Shipping Block
     *
     * @return string
     */
    public function getShippingBlock()
    {
        return \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\Shipping::class;
    }

    /**
     * Format Age
     *
     * @param int $age
     * @return string
     */
    public function formatAge($age)
    {
        $days = floor($age / (24 * 3600));
        $hours = floor($age / 3600) % 24;
        $mins = round($age / 60) % 60;
        $string = '';
        if ($days) {
            $string .= $days . 'd ';
        }
        $string .= $hours . 'h ';
        $string .= $mins . 'm';
        return $string;
    }
}
