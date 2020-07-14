<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\TransferStock\Block\Adminhtml\InventoryTransfer;

/**
 * Class AbstractInventoryTransfer
 * @package Magestore\TransferStock\Block\Adminhtml\InventoryTransfer
 */
class AbstractInventoryTransfer
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;


    /**
     * AbstractInventoryTransfer constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->request = $context->getRequest();
        $this->registry = $registry;
    }

    /**
     * @return \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferInterface
     */
    public function getInventoryTransfer()
    {
        return $this->registry->registry('current_inventory_transfer');
    }

    /**
     * Get inventory transfer status
     *
     * @return string
     */
    public function getInventoryTransferStatus()
    {
        $inventoryTransfer = $this->getInventoryTransfer();
        if($inventoryTransfer){
            return $inventoryTransfer->getStatus();
        }
        return \Magestore\TransferStock\Model\InventoryTransfer\Option\Status::STATUS_OPEN;
    }

    /**
     * Get inventory transfer status
     *
     * @return string
     */
    public function getInventoryTransferStage()
    {
        $inventoryTransfer = $this->getInventoryTransfer();
        if($inventoryTransfer){
            return $inventoryTransfer->getStage();
        }
        return \Magestore\TransferStock\Model\InventoryTransfer\Option\Stage::STAGE_NEW;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
