<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Sales\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magestore\Webpos\Model\Checkout\PosOrder;
use Magestore\Webpos\Model\Request\Actions\RefundAction;

/**
 * Class CreditmemoRepository
 *
 * @package Magestore\Webpos\Model\Sales\Order
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CreditmemoRepository implements \Magestore\Webpos\Api\Sales\Order\CreditmemoRepositoryInterface
{
    /**
     * @var \Magento\Sales\Model\Service\CreditmemoService
     */
    protected $creditmemoService;

    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Sales\Api\CreditmemoManagementInterface
     */
    protected $creditmemoManagement;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $invoiceRepositoryInterface;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Sales\Model\Order\RefundAdapterInterface
     */
    private $refundAdapter;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo
     */
    private $creditmemo;

    /**
     * @var \Magestore\Webpos\Model\Sales\Order\Creditmemo\EmailSender
     */
    private $creditmemoSender;

    /**
     * @var \Magestore\Webpos\Model\Customer\CustomerRepository
     */
    private $customerRepository;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Magestore\Webpos\Api\Sales\OrderRepositoryInterface
     */
    private $orderRepositoryInterface;

    /**
     * @var \Magestore\Webpos\Api\Data\Checkout\OrderInterfaceFactory
     */
    protected $posOrderInterfaceFactory;

    /**
     * @var \Magestore\Webpos\Api\Staff\SessionRepositoryInterface
     */
    protected $sessionRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterfaceFactory
     */
    protected $paymentInterfaceFactory;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;
    /**
     * @var \Magestore\Webpos\Model\Sales\OrderRepository
     */
    protected $posOrderRepository;
    /**
     * @var ServiceInputProcessor
     */
    protected $serviceInputProcessor;
    /**
     * @var \Magestore\Webpos\Model\Request\ActionLogFactory
     */
    protected $actionLogFactory;
    /**
     * @var \Magestore\Webpos\Log\Logger
     */
    protected $logger;

    /**
     * CreditmemoRepository constructor.
     *
     * @param \Magento\Sales\Model\Service\CreditmemoService $creditmemoService
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepositoryInterface
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Sales\Model\Order\RefundAdapterInterface $refundAdapter
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param Creditmemo\EmailSender $creditmemoSender
     * @param \Magestore\Webpos\Model\Customer\CustomerRepository $customerRepository
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magestore\Webpos\Api\Sales\OrderRepositoryInterface $orderRepositoryInterface
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterfaceFactory $posOrderInterfaceFactory
     * @param \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterfaceFactory $paymentInterfaceFactory
     * @param \Magento\Framework\Event\ManagerInterface $_eventManager
     * @param \Magestore\Webpos\Model\Sales\OrderRepository $posOrderRepository
     * @param ServiceInputProcessor $serviceInputProcessor
     * @param \Magestore\Webpos\Model\Request\ActionLogFactory $actionLogFactory
     * @param \Magestore\Webpos\Log\Logger $logger
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Sales\Model\Service\CreditmemoService $creditmemoService,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepositoryInterface,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Model\Order\RefundAdapterInterface $refundAdapter,
        \Magento\Sales\Model\Order\Creditmemo $creditmemo,
        \Magestore\Webpos\Model\Sales\Order\Creditmemo\EmailSender $creditmemoSender,
        \Magestore\Webpos\Model\Customer\CustomerRepository $customerRepository,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magestore\Webpos\Api\Sales\OrderRepositoryInterface $orderRepositoryInterface,
        \Magestore\Webpos\Api\Data\Checkout\OrderInterfaceFactory $posOrderInterfaceFactory,
        \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterfaceFactory $paymentInterfaceFactory,
        \Magento\Framework\Event\ManagerInterface $_eventManager,
        \Magestore\Webpos\Model\Sales\OrderRepository $posOrderRepository,
        ServiceInputProcessor $serviceInputProcessor,
        \Magestore\Webpos\Model\Request\ActionLogFactory $actionLogFactory,
        \Magestore\Webpos\Log\Logger $logger
    ) {
        $this->creditmemoService = $creditmemoService;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->creditmemoLoader = $creditmemoLoader;
        $this->request = $request;
        $this->creditmemoManagement = $creditmemoManagement;
        $this->resource = $resource;
        $this->invoiceRepositoryInterface = $invoiceRepositoryInterface;
        $this->priceCurrency = $priceCurrency;
        $this->refundAdapter = $refundAdapter;
        $this->creditmemo = $creditmemo;
        $this->creditmemoSender = $creditmemoSender;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->posOrderInterfaceFactory = $posOrderInterfaceFactory;
        $this->sessionRepository = $sessionRepository;
        $this->coreRegistry = $coreRegistry;
        $this->objectManager = $objectManager;
        $this->paymentInterfaceFactory = $paymentInterfaceFactory;
        $this->_eventManager = $_eventManager;
        $this->posOrderRepository = $posOrderRepository;
        $this->serviceInputProcessor = $serviceInputProcessor;
        $this->actionLogFactory = $actionLogFactory;
        $this->logger = $logger;
    }

    /**
     * Prepare creditmemo to refund and save it.
     *
     * @param \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoInterface $creditmemo
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createCreditmemoByOrderId($creditmemo)
    {
        try {
            /** @var \Magestore\Webpos\Api\Data\Checkout\OrderInterface $existedOrder */
            $existedOrder = $this->posOrderRepository->getWebposOrderByIncrementId($creditmemo->getOrderIncrementId());
        } catch (\Exception $e) {
            $existedOrder = false;
        }
        if (!$existedOrder || !$existedOrder->getEntityId()) {
            throw new LocalizedException(
                __('The order that you want to create credit memo has not been converted successfully!'),
                new \Exception(),
                \Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_CODE_SAVED_REQUEST_TO_SERVER
            );
        }

        $requestIncrementId = $creditmemo->getIncrementId();
        try {
            $result = $this->processCreditmemoRequest($requestIncrementId);

            if (!$result) {
                throw new LocalizedException(
                    __('Some things went wrong when trying to create new credit memo!'),
                    new \Exception(),
                    \Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_CODE_SAVED_REQUEST_TO_SERVER
                );
            }

            return $result;
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Some things went wrong when trying to create new credit memo!'),
                new \Exception(),
                \Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_CODE_SAVED_REQUEST_TO_SERVER
            );
        }
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function processCreditmemoRequest($requestIncrementId)
    {
        /** @var \Magestore\Webpos\Model\Request\ActionLog $actionLog */
        $actionLog = $this->actionLogFactory->create();
        $actionLog->load($requestIncrementId, 'request_increment_id');
        if (!$actionLog->getId() ||
            $actionLog->getActionType() != RefundAction::ACTION_TYPE ||
            $actionLog->getStatus() == \Magestore\Webpos\Model\Request\ActionLog::STATUS_COMPLETED) {
            return false;
        }

        // Modify request params
        $requestParams = $this->request->getParams();
        $requestLocationId = $actionLog->getLocationId();
        $requestParams[PosOrder::PARAM_ORDER_LOCATION_ID] = $requestLocationId;
        $this->request->setParams($requestParams);
        // End: Modify request params

        // Convert array to object parameter
        $params = json_decode($actionLog->getParams(), true);
        $params = $this->serviceInputProcessor->process(
            \Magestore\Webpos\Api\Sales\Order\CreditmemoRepositoryInterface::class,
            'createCreditmemoByOrderId',
            $params
        );
        /** @var \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoInterface $creditmemo */
        $creditmemo = $params[0];
        // End: Convert array to object parameter

        ////////////////////////////////
        /// Process Refund
        ////////////////////////////////
        try {
            $creditmemo = $this->modifyCreditmemoConvertingData($creditmemo);

            $this->coreRegistry->unregister('create_creditmemo_webpos');
            $this->coreRegistry->register('create_creditmemo_webpos', true);
            $this->coreRegistry->unregister('current_location_id');
            $this->coreRegistry->register('current_location_id', $requestLocationId);

            $creditmemoOffline = $creditmemo;
            $orderId = $creditmemo->getOrderId();
            $payments = $creditmemo->getPayments();
            $this->validateForRefund($creditmemoOffline);
            $data = $this->prepareCreditmemo($creditmemoOffline);
            $this->creditmemoLoader->setOrderId($data['order_id']);
            $this->creditmemoLoader->setCreditmemo($data['creditmemo']);
            $this->request->setParams($data);
            $creditmemo = $this->creditmemoLoader->load();
            if ($creditmemo) {
                if (!$creditmemo->isValidGrandTotal()) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('The credit memo\'s total must be positive.')
                    );
                }
                if (!empty($data['creditmemo']['comment_text'])) {
                    foreach ($data['creditmemo']['comment_text'] as $commentText) {
                        $creditmemo->addComment(
                            $commentText,
                            isset($data['creditmemo']['comment_customer_notify']),
                            true
                        );
                    }
                    if (isset($data['creditmemo']['comment_text'][0])) {
                        $creditmemo->setCustomerNote($data['creditmemo']['comment_text'][0]);
                    }
                    if (isset($data['creditmemo']['comment_customer_notify'])) {
                        $creditmemo->setCustomerNoteNotify(isset($data['comment_customer_notify']));
                    }
                }
                if ($creditmemoOffline->getIncrementId()) {
                    $creditmemo->setIncrementId($creditmemoOffline->getIncrementId());
                }
                $creditmemo->setPosLocationId($requestLocationId);
                $this->creditmemoManagement->refund(
                    $creditmemo,
                    true
                );

                $order = $creditmemo->getOrder();

                if ($payments && count($payments)) {
                    $this->createWebposOrderPayment($order, $payments);
                }

                if (!empty($data['creditmemo']['send_email'])) {
                    $this->creditmemoSender->send($creditmemo);
                }
            }

            $order = $this->orderRepositoryInterface->get($orderId);

            // Update action log
            $actionLog->setStatus(\Magestore\Webpos\Model\Request\ActionLog::STATUS_COMPLETED)->save();

            return $this->verifyOrderReturn($order);
        } catch (\Exception $e) {
            $this->logger->info($creditmemo->getOrderIncrementId());
            $this->logger->info($e->getMessage());
            $this->logger->info($e->getTraceAsString());
            $this->logger->info('___________________________________________');
            // Update action log
            $actionLog->setStatus(\Magestore\Webpos\Model\Request\ActionLog::STATUS_FAILED)->save();
            return false;
        }
    }

    /**
     * Modify credit memo data
     *
     * @param \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoInterface $creditmemo
     * @return \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoInterface
     * @throws \Exception
     */
    public function modifyCreditmemoConvertingData($creditmemo)
    {
        $orderId = $creditmemo->getOrderId();
        $existedOrder = $this->posOrderRepository->getWebposOrderByIncrementId($creditmemo->getOrderIncrementId());

        if ($existedOrder->getEntityId() !== $orderId) {
            // Have to correct order data in credit memo param
            $creditmemo->setOrderId($existedOrder->getEntityId());

            $orderItems = [];
            foreach ($existedOrder->getAllItems() as $item) {
                $orderItems[$item->getTmpItemId()] = $item->getItemId();
            }

            $creditmemoItems = [];
            foreach ($creditmemo->getItems() as $cmItem) {
                if ($cmItem->getOrderItemId() && isset($orderItems[$cmItem->getOrderItemId()])) {
                    $cmItem->setOrderItemId($orderItems[$cmItem->getOrderItemId()]);
                }
                $creditmemoItems[] = $cmItem;
            }
            $creditmemo->setItems($creditmemoItems);
        }

        return $creditmemo;
    }

    /**
     * Validate for refund
     *
     * @param \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoInterface $creditmemo
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateForRefund($creditmemo)
    {
        if ($creditmemo->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We cannot register an existing credit memo.')
            );
        }
    }

    /**
     * Prepare credit memo
     *
     * @param \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoInterface $creditmemo
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function prepareCreditmemo(\Magestore\Webpos\Api\Data\Sales\Order\CreditmemoInterface $creditmemo)
    {
        $data = [];
        $items = $creditmemo->getItems();
        $orderId = $creditmemo->getOrderId();
        $result = new \Magento\Framework\DataObject();
        if (count($items) > 0 && $orderId) {
            $data['order_id'] = $orderId;
            $creditmemoData = [];
            foreach ($items as $item) {
                $creditmemoData['items'][$item->getOrderItemId()]['qty'] = $item->getQty();
                if ($item->getBackToStock()) {
                    $creditmemoData['items'][$item->getOrderItemId()]['back_to_stock'] = 1;
                }
            }
            $creditmemoData['send_email'] = $creditmemo->getEmailSent();
            $comments = $creditmemo->getComments();
            if (count($comments)) {
                foreach ($comments as $comment) {
                    if (!isset($creditmemoData['comment_text'])) {
                        $creditmemoData['comment_text'] = [$comment->getComment()];
                    } else {
                        $creditmemoData['comment_text'][] = $comment->getComment();
                    }
                }
            }
            if ($creditmemoData['send_email']) {
                $creditmemoData['comment_customer_notify'] = 1;
            }
            /*$creditmemoData['shipping_amount'] = $creditmemo->getBaseShippingAmount();*/
            /** @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig */
            $taxConfig = $this->objectManager->create(\Magento\Tax\Model\Config::class);
            if ($taxConfig->displaySalesShippingInclTax($creditmemo->getOrder()->getStoreId())) {
                $creditmemoData['shipping_amount'] = $creditmemo->getBaseShippingInclTax();
            } else {
                $creditmemoData['shipping_amount'] = $creditmemo->getBaseShippingAmount();
            }
            $creditmemoData['adjustment_positive'] = $creditmemo->getBaseAdjustmentPositive();
            $creditmemoData['adjustment_negative'] = $creditmemo->getBaseAdjustmentNegative();
            $data['creditmemo'] = $creditmemoData;

            $result->setData($data);
            $eventData = [
                'result' => $result,
                'creditmemo' => $creditmemo
            ];
            $this->_eventManager->dispatch('pos_prepare_creditmemo_data_after', $eventData);
        }
        return $result->getData();
    }

    /**
     * Send email
     *
     * @param int $creditmemoIncrementId
     * @param string $email
     * @param string|null $incrementId
     * @return bool
     */
    public function sendEmail($creditmemoIncrementId, $email, $incrementId = '')
    {
        $creditmemo = $this->getByIncrementId($creditmemoIncrementId);
        if ($creditmemo) {
            $emailSender = $this->creditmemoSender;
            return $emailSender->sendCreditmemoToAnotherEmail($creditmemo, $email);
        }
        return false;
    }

    /**
     * Loads a specified credit memo.
     *
     * @param int $incrementId The credit memo Increment Id.
     * @return \Magento\Sales\Api\Data\CreditmemoInterface|null Credit memo interface.
     */
    public function getByIncrementId($incrementId)
    {
        $creditmemo = $this->creditmemo->load($incrementId, 'increment_id');
        if ($creditmemo->getId()) {
            return $creditmemo;
        }
        return null;
    }

    /**
     * Create customer
     *
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerInterface $customer
     * @param string $incrementId
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerInterface
     * @throws \Exception
     */
    public function createCustomer($customer, $incrementId)
    {
        if (!$customer->getId()) {
            $customer = $this->customerRepository->save($customer);
        }
        if ($customer->getId() && $incrementId) {
            $order = $this->orderRepositoryInterface->getMagentoOrderByIncrementId($incrementId);
            $order->setCustomerId($customer->getId());
            $order->setCustomerEmail($customer->getEmail());
            $order->setCustomerGroupId($customer->getGroupId());
            $order->setCustomerFirstname($customer->getFirstname());
            $order->setCustomerLastname($customer->getLastname());
            $order->setCustomerIsGuest(0);
            try {
                $this->orderRepository->save($order);
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
            }
            return $customer;
        } else {
            throw new LocalizedException(__('Can not create customer'));
        }
    }

    /**
     * Get correct status from order
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface
     */
    public function verifyOrderReturn($order)
    {
        $posOrder = $this->posOrderInterfaceFactory->create();
        $posOrder->setData($order->getData());
        $posOrder->setAddresses($order->getAddresses());
        $posOrder->setPayments($order->getPayments());
        $posOrder->setItems($order->getItems());
        $posOrder->setStatusHistories($order->getStatusHistories());
        return $posOrder;
    }

    /**
     * Create order payment
     *
     * @param \Magento\Sales\Model\Order $order
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterface[] $payments
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createWebposOrderPayment(&$order, $payments)
    {
        if (count($payments)) {
            /** @var \Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterface $payment */
            foreach ($payments as $payment) {
                $data = $payment->getData();
                $data['title'] = $payment->getTitle();
                $data['base_amount_paid'] = $payment->getBaseAmountPaid();
                $data['amount_paid'] = $payment->getAmountPaid();
                /** @var \Magestore\Webpos\Model\Checkout\Order\Payment $paymentModel */
                $paymentModel = $this->paymentInterfaceFactory->create();
                $paymentModel->setData($data);
                $paymentModel->setOrderId($order->getId());
                $this->_eventManager->dispatch(
                    'creditmemo_webpos_payment_save_before',
                    [
                        'webpos_payment' => $paymentModel,
                        'payment_data' => $payment
                    ]
                );
                try {
                    $paymentModel->getResource()->save($paymentModel);
                } catch (\Exception $exception) {
                    throw new \Magento\Framework\Exception\CouldNotSaveException(__($exception->getMessage()));
                }

                if ($data['method'] == 'store_credit' && $order->getCustomerId()) {
                    $this->changeCustomerCredit($data['base_amount_paid'], $order);
                }
            }
        }
    }

    /**
     * Change customer credit
     *
     * @param float $creditAmount
     * @param \Magento\Sales\Model\Order $order
     */
    public function changeCustomerCredit($creditAmount, $order)
    {
        $transaction = $this->objectManager->get(\Magestore\Customercredit\Model\TransactionFactory::class)
            ->create();
        $customerCredit = $this->objectManager->get(\Magestore\Customercredit\Model\CustomercreditFactory::class)
            ->create();
        $customerId = $order->getCustomerId();
        $orderId = $order->getId();
        $transactionDetail = __("Refund order #%1", $order->getIncrementId());
        $transaction->addTransactionHistory($customerId, 5, $transactionDetail, $orderId, $creditAmount);
        $customerCredit->changeCustomerCredit($creditAmount, $customerId);
    }
}
