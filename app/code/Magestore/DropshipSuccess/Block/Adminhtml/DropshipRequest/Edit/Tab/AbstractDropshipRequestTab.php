<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\Edit\Tab;

/**
 * Class AbstractDropshipRequestTab
 * @package Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\Edit\Tab
 */
abstract class AbstractDropshipRequestTab extends \Magento\Backend\Block\Widget
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magestore\DropshipSuccess\Api\DropshipRequestRepositoryInterface
     */
    protected $dropshipRequestRepository;

    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $addressRenderer;

    /**
     * @var \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface
     */
    protected $supplierRepository;

    /**
     * @var \Magestore\DropshipSuccess\Service\DropshipRequest\DropshipRequestItemService
     */
    protected $dropshipRequestItemService;

    /**
     * @var \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface
     */
    protected $dropshipRequestInterface;

    /**
     * @var \Magento\Sales\Api\OrderItemRepositoryInterface
     */
    protected $orderItemRepository;


    /**
     * AbstractDropshipRequestTab constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magestore\DropshipSuccess\Api\DropshipRequestRepositoryInterface $dropshipRequestRepository
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository
     * @param \Magestore\DropshipSuccess\Service\DropshipRequest\DropshipRequestItemService $dropshipRequestItemService
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface $dropshipRequestInterface
     * @param \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magestore\DropshipSuccess\Api\DropshipRequestRepositoryInterface $dropshipRequestRepository,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository,
        \Magestore\DropshipSuccess\Service\DropshipRequest\DropshipRequestItemService $dropshipRequestItemService,
        \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface $dropshipRequestInterface,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->orderRepository = $orderRepository;
        $this->dropshipRequestRepository = $dropshipRequestRepository;
        $this->addressRenderer = $addressRenderer;
        $this->supplierRepository = $supplierRepository;
        $this->dropshipRequestItemService = $dropshipRequestItemService;
        $this->dropshipRequestInterface = $dropshipRequestInterface;
        $this->orderItemRepository = $orderItemRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getDropshipRequest()
    {
        return $this->coreRegistry->registry(\Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface::CURRENT_DROPSHIP_REQUEST);
    }

}
