<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposPaynl\Model\Paymentmethod;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order;
use Magestore\WebposPaynl\Model\Config;

/**
 * Description of AbstractPaymentMethod
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PaymentMethod extends AbstractMethod
{
    protected $_isInitializeNeeded = true;

    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    /**
     * @var Config
     */
    protected $paynlConfig;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;
    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $orderConfig;

    /**
     * PaymentMethod constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param Order\Config $orderConfig
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );

        $this->orderRepository = $orderRepository;
        $this->orderConfig = $orderConfig;
        $this->paynlConfig = new Config($this->_scopeConfig);
    }

    /**
     * Get State
     *
     * @param string $status
     * @return bool|mixed
     */
    public function getState($status)
    {
        $validStates = [
            Order::STATE_NEW,
            Order::STATE_PENDING_PAYMENT,
            Order::STATE_HOLDED
        ];

        foreach ($validStates as $state) {
            $statusses = $this->orderConfig->getStateStatuses($state, false);
            if (in_array($status, $statusses)) {
                return $state;
            }
        }
        return false;
    }

    /**
     * Get payment instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }

    /**
     * Get bank
     *
     * @return array
     */
    public function getBanks()
    {
        return [];
    }

    /**
     * Initialize
     *
     * @param string $paymentAction
     * @param \Magento\Framework\DataObject $stateObject
     * @return PaymentMethod
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function initialize($paymentAction, $stateObject)
    {
        $status = $this->getConfigData('order_status');

        $stateObject->setState($this->getState($status));
        $stateObject->setStatus($status);
        $stateObject->setIsNotified(false);

        $sendEmail = $this->_scopeConfig->getValue('payment/' . $this->_code . '/send_new_order_email', 'store');

        $payment = $this->getInfoInstance();
        /** @var Order $order */
        $order = $payment->getOrder();

        if ($sendEmail == 'after_payment') {
            //prevent sending the order confirmation
            $order->setCanSendNewEmailFlag(false);
        }

        $this->orderRepository->save($order);

        return parent::initialize($paymentAction, $stateObject);
    }

    /**
     * Refund
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return bool|PaymentMethod
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->paynlConfig->configureSDK();

        $transactionId = $payment->getParentTransactionId();

        \Paynl\Transaction::refund($transactionId, $amount); // phpstan:ignore

        return true;
    }

    /**
     * StartTransaction
     *
     * @param \Magento\Sales\Model\Order $order
     * @param float $total
     * @param string $currency
     * @param int $bankId
     * @return mixed
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function startTransaction($order, $total, $currency, $bankId)
    {
        $transaction = $this->doStartTransaction($order, $total, $currency, $bankId);

        $holded = $this->_scopeConfig->getValue('payment/' . $this->_code . '/holded', 'store');
        if ($holded) {
            $order->hold();
        }
        $this->orderRepository->save($order);

        return $transaction->getRedirectUrl();
    }

    /**
     * Do Start Transaction
     *
     * @param \Magento\Sales\Model\Order $order
     * @param float $total
     * @param string $currency
     * @param int $bankId
     * @return mixed
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function doStartTransaction($order, $total, $currency, $bankId)
    {
        $this->paynlConfig->configureSDK();
        $expireDate = '';

        $items = $order->getAllVisibleItems();

        $orderId = $order->getIncrementId();
        $quoteId = $order->getId();

        $store = $order->getStore();
        $baseUrl = $store->getBaseUrl();
        // i want to use the url builder here, but that doenst work from admin, even if the store is supplied
        $returnUrl = $baseUrl . 'paynl/checkout/finish/';
        $exchangeUrl = $baseUrl . 'paynl/checkout/exchange/';

        $arrBillingAddress = $order->getBillingAddress();
        if ($arrBillingAddress) {
            $arrBillingAddress = $arrBillingAddress->toArray();

            // Use default initials
            $strBillingFirstName = substr($arrBillingAddress['firstname'], 0, 1);

            $enduser = [
                'initials' => $strBillingFirstName,
                'lastName' => $arrBillingAddress['lastname'],
                'phoneNumber' => $arrBillingAddress['telephone'],
                'emailAddress' => $arrBillingAddress['email'],
            ];

            $invoiceAddress = [
                'initials' => $strBillingFirstName,
                'lastName' => $arrBillingAddress['lastname']
            ];

            $arrAddress = \Paynl\Helper::splitAddress($arrBillingAddress['street']); // phpstan:ignore
            $invoiceAddress['streetName'] = $arrAddress[0];
            $invoiceAddress['houseNumber'] = $arrAddress[1];
            $invoiceAddress['zipCode'] = $arrBillingAddress['postcode'];
            $invoiceAddress['city'] = $arrBillingAddress['city'];
            $invoiceAddress['country'] = $arrBillingAddress['country_id'];
        }

        $arrShippingAddress = $order->getShippingAddress();
        if ($arrShippingAddress) {
            $arrShippingAddress = $arrShippingAddress->toArray();

            // Use default initials
            $strShippingFirstName = substr($arrShippingAddress['firstname'], 0, 1);

            $shippingAddress = [
                'initials' => $strShippingFirstName,
                'lastName' => $arrShippingAddress['lastname']
            ];
            $arrAddress2 = \Paynl\Helper::splitAddress($arrShippingAddress['street']); // phpstan:ignore
            $shippingAddress['streetName'] = $arrAddress2[0];
            $shippingAddress['houseNumber'] = $arrAddress2[1];
            $shippingAddress['zipCode'] = $arrShippingAddress['postcode'];
            $shippingAddress['city'] = $arrShippingAddress['city'];
            $shippingAddress['country'] = $arrShippingAddress['country_id'];
        }

        $data = [
            'amount' => $total,
            'returnUrl' => $returnUrl,
            'paymentMethod' => 1729,
            'language' => $this->paynlConfig->getLanguage(),
            'bank' => $bankId,
            'expireDate' => $expireDate,
            'description' => $orderId,
            'extra1' => $orderId,
            'extra2' => $quoteId,
            'extra3' => $order->getEntityId(),
            'exchangeUrl' => $exchangeUrl,
            'currency' => $currency,
        ];
        if (isset($shippingAddress)) {
            $data['address'] = $shippingAddress;
        }
        if (isset($invoiceAddress)) {
            $data['invoiceAddress'] = $invoiceAddress;
        }
        if (isset($enduser)) {
            $data['enduser'] = $enduser;
        }
        $arrProducts = [];
        foreach ($items as $item) {
            $arrItem = $item->toArray();
            if ($arrItem['price_incl_tax'] != null) {
                // taxamount is not valid, because on discount it returns the taxamount after discount
                $taxAmount = $arrItem['price_incl_tax'] - $arrItem['price'];
                $product = [
                    'id' => $arrItem['product_id'],
                    'name' => $arrItem['name'],
                    'price' => $arrItem['price_incl_tax'],
                    'qty' => $arrItem['qty'],
                    'tax' => $taxAmount,
                ];
                $arrProducts[] = $product;
            }
        }

        //shipping
        $shippingCost = $order->getShippingInclTax();
        $shippingTax = $order->getShippingTaxAmount();
        $shippingDescription = $order->getShippingDescription();

        if ($shippingCost != 0) {
            $arrProducts[] = [
                'id' => 'shipping',
                'name' => $shippingDescription,
                'price' => $shippingCost,
                'qty' => 1,
                'tax' => $shippingTax
            ];
        }

        // kortingen
        $discount = $order->getDiscountAmount();
        $discountDescription = $order->getDiscountDescription();

        if ($discount != 0) {
            $arrProducts[] = [
                'id' => 'discount',
                'name' => $discountDescription,
                'price' => $discount,
                'qty' => 1,
                'tax' => $order->getDiscountTaxCompensationAmount() * -1
            ];
        }

        $data['products'] = $arrProducts;

        if ($this->paynlConfig->isTestMode()) {
            $data['testmode'] = 1;
        }
        $ipAddress = $order->getRemoteIp();
        //The ip address field in magento is too short, if the ip is invalid, get the ip myself
        if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            $ipAddress = \Paynl\Helper::getIp(); // phpstan:ignore
        }
        $data['ipaddress'] = $ipAddress;
        $transaction = \Paynl\Transaction::start($data); // phpstan:ignore

        return $transaction;
    }

    /**
     * GetPaymentOptionId
     *
     * @return int|float|string
     */
    public function getPaymentOptionId()
    {
        return $this->getConfigData('payment_option_id');
    }
}
