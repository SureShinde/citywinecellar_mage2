<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\AdjustStock\Block\Adminhtml\AdjustStock;


use Magestore\AdjustStock\Model\ResourceModel\AdjustStock;
use Magento\Framework\View\Element\UiComponent\Context;

/**
 * Class AbstractAdjustStock
 * @package Magestore\AdjustStock\Block\Adminhtml\AdjustStock
 */
class AbstractAdjustStock extends \Magento\Backend\Block\Template
{
    /**
     * Url Builder
     *
     * @var Context
     */
    protected $context;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;


    /**
     * AbstractAdjustStock constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->_authorization = $context->getAuthorization();
        parent::__construct($context, $data);
    }

    /**
     * Get adjust stock
     *
     * @return \Magestore\AdjustStock\Api\Data\AdjustStock\AdjustStockInterface
     */
    public function getAdjustStock()
    {
        return $this->registry->registry('current_adjuststock');
    }

    /**
     * Get adjust stock status
     *
     * @return string
     */
    public function getAdjustStockStatus()
    {
        $adjustStock = $this->getAdjustStock();
        if($adjustStock){
            return $adjustStock->getStatus();
        }
            return '0';
    }

    /**
     * Get current Warehouse which creating adjust
     *
     * @return \Magento\InventoryApi\Api\Data\SourceInterface
     */
    public function getSource()
    {
        return $this->registry->registry('current_source');
    }
}
