<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Plugin\InventorySales\Model\ResourceModel;

/**
 * Class StockIdResolver
 *
 * @package Magestore\Webpos\Plugin\InventorySales\Model\ResourceModel
 */
class StockIdResolver
{
    /**
     * @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface
     */
    protected $stockManagement;
    /***
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * StockIdResolver constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement,
        \Magento\Framework\Registry $registry
    ) {
        $this->request = $request;
        $this->stockManagement = $stockManagement;
        $this->orderRepository = $orderRepository;
        $this->registry = $registry;
    }

    /**
     * Around resolve stock
     *
     * @param \Magento\InventorySales\Model\ResourceModel\StockIdResolver $subject
     * @param callable $proceed
     * @param string $type
     * @param string $code
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundResolve(
        \Magento\InventorySales\Model\ResourceModel\StockIdResolver $subject,
        callable $proceed,
        string $type,
        string $code
    ) {
        if ($this->registry->registry('pos_create_shipment_place_reservation')) {
            return $proceed($type, $code);
        }

        if ($this->request->getControllerName().'_'.$this->request->getActionName() == 'order_invoice_save') {
            $orderId = $this->request->getParam('order_id');
            $order = $this->orderRepository->get($orderId);
            if ($order->getId()) {
                $stockId = $this->stockManagement->getStockIdFromOrder($order);
            }
        } else {
            $stockId = $this->stockManagement->getStockId();
        }

        return $stockId ? $stockId : $proceed($type, $code);
    }
}
