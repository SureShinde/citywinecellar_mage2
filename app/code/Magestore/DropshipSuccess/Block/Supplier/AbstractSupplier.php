<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Supplier;

use Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface;
use Magestore\DropshipSuccess\Api\DropshipRequestRepositoryInterface;
use Magestore\DropshipSuccess\Service\DropshipRequestService;
use Magestore\DropshipSuccess\Service\PricelistUploadService;
use Magestore\OrderSuccess\Api\OrderRepositoryInterface;
use Magestore\SupplierSuccess\Model\Session;
use Magestore\SupplierSuccess\Service\Supplier\ProductService;

/**
 * Class AbstractSupplier
 * @package Magestore\DropshipSuccess\Block\Supplier
 */
abstract class AbstractSupplier extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Session
     */
    protected $supplierSession;

    /**
     * @var \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\CollectionFactory
     */
    protected $dropshipCollectionFactory;

    /**
     * @var DropshipRequestService
     */
    protected $dropshipRequestService;

    /**
     * @var DropshipRequestRepositoryInterface
     */
    protected $dropshipRequestRepositoryInterface;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $addressRenderer;

    /**
     * @var DropshipRequestInterface
     */
    protected $dropshipRequestInterface;

    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @var PricelistUploadService
     */
    protected $pricelistUploadService;

    /**
     * AbstractSupplier constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Session $supplierSession
     * @param \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\CollectionFactory $dropshipCollectionFactory
     * @param DropshipRequestService $dropshipRequestService
     * @param DropshipRequestRepositoryInterface $dropshipRequestRepositoryInterface
     * @param OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param DropshipRequestInterface $dropshipRequestInterface
     * @param ProductService $productService
     * @param PricelistUploadService $pricelistUploadService
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Session $supplierSession,
        \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\CollectionFactory $dropshipCollectionFactory,
        DropshipRequestService $dropshipRequestService,
        DropshipRequestRepositoryInterface $dropshipRequestRepositoryInterface,
        OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        DropshipRequestInterface $dropshipRequestInterface,
        ProductService $productService,
        PricelistUploadService $pricelistUploadService,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->supplierSession = $supplierSession;
        $this->dropshipCollectionFactory = $dropshipCollectionFactory;
        $this->dropshipRequestService = $dropshipRequestService;
        $this->dropshipRequestRepositoryInterface = $dropshipRequestRepositoryInterface;
        $this->orderRepository = $orderRepository;
        $this->addressRenderer = $addressRenderer;
        $this->dropshipRequestInterface = $dropshipRequestInterface;
        $this->productService = $productService;
        $this->pricelistUploadService = $pricelistUploadService;
    }
}