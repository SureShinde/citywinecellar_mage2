<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Order\View;

/**
 * Class Info
 * @package Magestore\OrderSuccess\Block\Adminhtml\Sales\View
 */
class Info extends \Magento\Backend\Block\Template
{

    /**
     * \Magestore\OrderSuccess\Api\Data\BatchSourceInterface
     */
    protected $batchSourceInterface;


    /**
     * \Magestore\OrderSuccess\Api\Data\TagSourceInterface
     */
    protected $tagSourceInterface;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * Info constructor.
     * @param \Magestore\OrderSuccess\Api\Data\BatchSourceInterface $batchSourceInterface
     * @param \Magestore\OrderSuccess\Api\Data\TagSourceInterface $tagSourceInterface
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magestore\OrderSuccess\Api\Data\BatchSourceInterface $batchSourceInterface,
        \Magestore\OrderSuccess\Api\Data\TagSourceInterface $tagSourceInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->batchSourceInterface = $batchSourceInterface;
        $this->tagSourceInterface = $tagSourceInterface;
        $this->_coreRegistry = $registry;
    }

    /**
     * Get Order Id
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getOrder() ? $this->getOrder()->getId() : null;
    }

    /**
     * Get Sales
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('sales_order');
    }


}

