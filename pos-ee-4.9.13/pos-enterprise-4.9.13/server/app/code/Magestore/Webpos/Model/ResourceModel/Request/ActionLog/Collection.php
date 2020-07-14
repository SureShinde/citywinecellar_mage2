<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Request\ActionLog;

/**
 * Class Collection
 *
 * @package Magestore\Webpos\Model\ResourceModel\Request\ActionLog
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * @var \Magestore\Webpos\Api\Sales\Order\CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;
    /**
     * @var \Magestore\WebposShipping\Api\Order\ShippingServiceInterface
     */
    protected $shippingService;
    /**
     * @var \Magestore\Webpos\Api\Checkout\CheckoutRepositoryInterface
     */
    protected $checkoutRepository;
    /**
     * @var \Magestore\Webpos\Api\Sales\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magestore\Webpos\Api\Sales\Order\CreditmemoRepositoryInterface $creditmemoRepository
     * @param \Magestore\WebposShipping\Api\Order\ShippingServiceInterface $shippingService
     * @param \Magestore\Webpos\Api\Checkout\CheckoutRepositoryInterface $checkoutRepository
     * @param \Magestore\Webpos\Api\Sales\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magestore\Webpos\Api\Sales\Order\CreditmemoRepositoryInterface $creditmemoRepository,
        \Magestore\WebposShipping\Api\Order\ShippingServiceInterface $shippingService,
        \Magestore\Webpos\Api\Checkout\CheckoutRepositoryInterface $checkoutRepository,
        \Magestore\Webpos\Api\Sales\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->creditmemoRepository = $creditmemoRepository;
        $this->shippingService = $shippingService;
        $this->checkoutRepository = $checkoutRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Initialize collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magestore\Webpos\Model\Request\ActionLog::class,
            \Magestore\Webpos\Model\ResourceModel\Request\ActionLog::class
        );
    }

    /**
     * Process action log
     */
    public function processActionLog()
    {
        foreach ($this->getItems() as $actionLog) {
            switch ($actionLog->getActionType()) {
                case \Magestore\Webpos\Model\Request\Actions\TakePaymentAction::ACTION_TYPE:
                    $this->checkoutRepository->processTakePaymentActionLog($actionLog->getRequestIncrementId());
                    break;
                case \Magestore\Webpos\Model\Request\Actions\ShipmentAction::ACTION_TYPE:
                    $this->shippingService->processCreateShipmentActionLog($actionLog->getRequestIncrementId());
                    break;
                case \Magestore\Webpos\Model\Request\Actions\RefundAction::ACTION_TYPE:
                    $this->creditmemoRepository->processCreditmemoRequest($actionLog->getRequestIncrementId());
                    break;
                case \Magestore\Webpos\Model\Request\Actions\CancelAction::ACTION_TYPE:
                    $this->orderRepository->processCancelOrderActionLog($actionLog->getRequestIncrementId());
                    break;
                default:
                    break;
            }
        }
    }
}
