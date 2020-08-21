<?php
namespace Magestore\Rewardpoints\Model;

/**
 * Transaction model
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Transaction extends \Magento\Framework\Model\AbstractModel implements
    \Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface
{
    /**
     * @var Customer
     */
    protected $rewardAccountFactory;
    /**
     * @var \Magestore\Rewardpoints\Helper\Config
     */
    protected $helper;
    /**
     * @var \Magestore\Rewardpoints\Helper\Point
     */
    protected $helperPoint;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_modelCustomerFactory;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var \Magestore\Rewardpoints\Helper\Customer
     */
    protected $_rewardpointsHelperCustomer;
    /**
     * @var \Magestore\Rewardpoints\Helper\Action
     */
    protected $_helperAction;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    const STATUS_PENDING                    = 1;
    const STATUS_ON_HOLD                    = 2;
    const STATUS_COMPLETED                  = 3;
    const STATUS_CANCELED                   = 4;
    const STATUS_EXPIRED                    = 5;
    const ACTION_TYPE_BOTH                  = 0;
    const ACTION_TYPE_EARN                  = 1;
    const ACTION_TYPE_SPEND                 = 2;
    const XML_PATH_MAX_BALANCE              = 'rewardpoints/earning/max_balance';
    const XML_PATH_EMAIL_ENABLE             = 'rewardpoints/email/enable';
    const XML_PATH_EMAIL_SENDER             = 'rewardpoints/email/sender';
    const XML_PATH_EMAIL_UPDATE_BALANCE_TPL = 'rewardpoints/email/update_balance';
    const XML_PATH_EMAIL_BEFORE_EXPIRE_TPL  = 'rewardpoints/email/before_expire_transaction';
    const XML_PATH_EMAIL_EXPIRE_DAYS        = 'rewardpoints/email/before_expire_days';

    /**
     * Redefine event Prefix, event object
     *
     * @var string
     */
    protected $_eventPrefix = 'rewardpoints_transaction';
    protected $_eventObject = 'rewardpoints_transaction';

    /**
     * Transaction constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Action\Context $contextAction
     * @param CustomerFactory $rewardpointsCustomerFactory
     * @param \Magestore\Rewardpoints\Helper\Config $helperConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Rewardpoints\Helper\Point $helperPoint
     * @param \Magento\Customer\Model\CustomerFactory $modelCustomerFactory
     * @param \Magestore\Rewardpoints\Helper\Customer $rewardpointsHelperCustomer
     * @param \Magestore\Rewardpoints\Helper\Action $helperAction
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Action\Context $contextAction,
        \Magestore\Rewardpoints\Model\CustomerFactory $rewardpointsCustomerFactory,
        \Magestore\Rewardpoints\Helper\Config $helperConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        \Magento\Customer\Model\CustomerFactory $modelCustomerFactory,
        \Magestore\Rewardpoints\Helper\Customer $rewardpointsHelperCustomer,
        \Magestore\Rewardpoints\Helper\Action $helperAction,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->messageManager              = $contextAction->getMessageManager();
        $this->rewardAccountFactory               = $rewardpointsCustomerFactory;
        $this->helper                      = $helperConfig;
        $this->_transportBuilder           = $transportBuilder;
        $this->_storeManager               = $storeManager;
        $this->helperPoint                 = $helperPoint;
        $this->_modelCustomerFactory       = $modelCustomerFactory;
        $this->_rewardpointsHelperCustomer = $rewardpointsHelperCustomer;
        $this->_helperAction               = $helperAction;
        $this->dateTime = $dateTime;
        $this->inlineTranslation = $inlineTranslation;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magestore\Rewardpoints\Model\ResourceModel\Transaction::class);
    }

    /**
     * Get transaction status as hash array
     *
     * @return array
     */
    public function getStatusHash()
    {
        return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_ON_HOLD => __('On Hold'),
            self::STATUS_COMPLETED => __('Complete'),
            self::STATUS_CANCELED => __('Canceled'),
            self::STATUS_EXPIRED => __('Expired'),
        ];
    }

    /**
     * Get Status Array
     *
     * @return array
     */
    public function getStatusArray()
    {
        $options = [];
        foreach ($this->getStatusHash() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label,
            ];
        }
        return $options;
    }

    /**
     * Get Const
     *
     * @param string $const
     * @return bool|mixed
     */
    public function getConst($const)
    {
        $data = [
            'STATUS_PENDING' => self::STATUS_PENDING,
            'STATUS_ON_HOLD' => self::STATUS_ON_HOLD,
            'STATUS_COMPLETED' => self::STATUS_COMPLETED,
            'STATUS_CANCELED' => self::STATUS_CANCELED,
            'STATUS_EXPIRED' => self::STATUS_EXPIRED,
        ];
        if (isset($data[$const]) && $data[$const]) {
            return $data[$const];
        } else {
            return false;
        }
    }

    /**
     * Complete Transaction
     *
     * @return $this|bool
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function completeTransaction()
    {

        if (!$this->getId()
            || !$this->getCustomerId()
            || !$this->getRewardId()
            || $this->getPointAmount() <= 0
            || !in_array($this->getStatus(), [self::STATUS_PENDING, self::STATUS_ON_HOLD])
        ) {
            $this->messageManager->addError(__('Invalid transaction data to complete.'));
            return false;
        }
        $rewardAccount = $this->getRewardAccount();
        if ($this->getData('status') == self::STATUS_ON_HOLD) {
            $rewardAccount->setHoldingBalance($rewardAccount->getHoldingBalance() - $this->getRealPoint());
        }
        // dispatch event when complete a transaction
        $this->_eventManager->dispatch(
            $this->_eventPrefix . '_complete_' . $this->getData('action'),
            $this->_getEventData()
        );

        $this->setStatus(self::STATUS_COMPLETED);

        $maxBalance = (int)$this->helper->getConfig(self::XML_PATH_MAX_BALANCE, $this->getStoreId());

        if ($maxBalance > 0
            && $this->getRealPoint() > 0
            && $rewardAccount->getPointBalance() + $this->getRealPoint() > $maxBalance
        ) {
            if ($maxBalance > $rewardAccount->getPointBalance()) {
                $this->setPointAmount(
                    $maxBalance - $rewardAccount->getPointBalance() + $this->getPointAmount() - $this->getRealPoint()
                );
                $this->setRealPoint($maxBalance - $rewardAccount->getPointBalance());
                $rewardAccount->setPointBalance($maxBalance);
                $this->sendUpdateBalanceEmail($rewardAccount);
            } else {
                $this->messageManager->addError(__('Maximum points allowed in account balance is %1.', $maxBalance));
                return false;
            }
        } else {
            $rewardAccount->setPointBalance($rewardAccount->getPointBalance() + $this->getRealPoint());
            $this->sendUpdateBalanceEmail($rewardAccount);
        }

        // Save reward account and transaction to database
        $rewardAccount->save();

        $this->save();
        return $this;
    }

    /**
     * Get Reward Account
     *
     * @return mixed
     */
    public function getRewardAccount()
    {
        if (!$this->hasData('reward_account')) {
            $this->setData(
                'reward_account',
                $this->rewardAccountFactory->create()->load($this->getRewardId())
            );
        }
        return $this->getData('reward_account');
    }

    /**
     * Send Update Balance to customer
     *
     * @param \Magestore\RewardPoints\Model\Customer $rewardAccount
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function sendUpdateBalanceEmail($rewardAccount = null)
    {

        if (!$this->helper->getConfig(self::XML_PATH_EMAIL_ENABLE, $this->getStoreId())) {
            return $this;
        }

        if ($rewardAccount === null) {
            $rewardAccount = $this->getRewardAccount();
        }

        if (!$rewardAccount->getIsNotification()) {
            return $this;
        }

        $customer = $this->getCustomer();
        if (!$customer) {
            $customer = $this->_modelCustomerFactory->create()->load($rewardAccount->getCustomerId());
        }
        if (!$customer->getId()) {
            return $this;
        }

        $store = $this->_storeManager->getStore()->getId();

        $customerName = '';
        if ($customer instanceof \Magento\Customer\Model\Customer) {
            $customerName = $customer->getName();
        } elseif ($customer instanceof \Magento\Customer\Model\Data\Customer) {
            if ($customer->getPrefix()) {
                $customerName = $customer->getPrefix() . ' ';
            }
            if ($customer->getFirstname()) {
                $customerName .= $customer->getFirstname() . ' ';
            }
            if ($customer->getMiddlename()) {
                $customerName .= $customer->getMiddlename() . ' ';
            }
            if ($customer->getLastname()) {
                $customerName .= $customer->getLastname();
            }
        }
        $templateId = $this->helper->getConfig(self::XML_PATH_EMAIL_UPDATE_BALANCE_TPL, $store);
        try {
            $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
                ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
                ->setTemplateVars(
                    [
                        'store' => $this->_storeManager->getStore(),
                        'customer' => $customer,
                        'customerName' => $customerName,
                        'title' => $this->getTitle(),
                        'amount' => $this->getPointAmount(),
                        'total' => $rewardAccount->getPointBalance(),
                        'point_amount' => $this->helperPoint->format($this->getPointAmount(), $store),
                        'point_balance' => $this->helperPoint->format($rewardAccount->getPointBalance(), $store),
                        'status' => $this->getStatusLabel(),
                    ]
                )
                ->setFrom($this->helper->getConfig(self::XML_PATH_EMAIL_SENDER, $store))
                ->addTo($customer->getEmail(), $customerName)
                ->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $this;
        }
        return $this;
    }

    /**
     * Send email to customer before transaction is expired
     *
     * @param \Magestore\RewardPoints\Model\Customer $rewardAccount
     * @return \Magestore\RewardPoints\Model\Transaction
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function sendBeforeExpireEmail()
    {
        if (!$this->helper->getConfig(self::XML_PATH_EMAIL_ENABLE, $this->getStoreId())) {
            return $this;
        }

        $rewardAccount = $this->getRewardAccount();

        if (!$rewardAccount->getIsNotification()) {
            return $this;
        }

        $customer = $this->getCustomer();

        if (!$customer) {
            $customer = $this->_modelCustomerFactory->create()->load($rewardAccount->getCustomerId());
        }
        if (!$customer->getId()) {
            return $this;
        }

        $store = $this->_storeManager->getStore()->getId();

        $this->inlineTranslation->suspend();

        $customerName = '';
        if ($customer instanceof \Magento\Customer\Model\Customer) {
            $customerName = $customer->getName();
        } elseif ($customer instanceof \Magento\Customer\Model\Data\Customer) {
            if ($customer->getPrefix()) {
                $customerName = $customer->getPrefix() . ' ';
            }
            if ($customer->getFirstname()) {
                $customerName .= $customer->getFirstname() . ' ';
            }
            if ($customer->getMiddlename()) {
                $customerName .= $customer->getMiddlename() . ' ';
            }
            if ($customer->getLastname()) {
                $customerName .= $customer->getLastname();
            }
        }

        $templateId = $this->helper->getConfig(self::XML_PATH_EMAIL_BEFORE_EXPIRE_TPL, $store);
        try {
            $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
                ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
                ->setTemplateVars(
                    [
                        'store' => $this->_storeManager->getStore(),
                        'customer' => $customer,
                        'customerName' => $customerName,
                        'title' => $this->getTitle(),
                        'amount' => $this->getPointAmount(),
                        'spent' => $this->getPointUsed(),
                        'total' => $rewardAccount->getPointBalance(),
                        'point_amount' => $this->helperPoint->format($this->getPointAmount(), $store),
                        'point_used' => $this->helperPoint->format($this->getPointUsed(), $store),
                        'point_balance' => $this->helperPoint->format($rewardAccount->getPointBalance(), $store),
                        'status' => $this->getStatusLabel(),
                        'expirationdays' => round((strtotime($this->getExpirationDate()) - time()) / 86400),
                        'expirationdate' => $this->dateTime->date('M d, Y H:i:s', $this->getExpirationDate()),
                    ]
                )
                ->setFrom($this->helper->getConfig(self::XML_PATH_EMAIL_SENDER, $store))
                ->addTo($customer->getEmail(), $customerName)
                ->getTransport();

            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this;
        }

        $this->inlineTranslation->resume();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatusLabel()
    {
        $statushash = $this->getStatusHash();
        if (isset($statushash[$this->getStatus()])) {
            return $statushash[$this->getStatus()];
        }
        return '';
    }

    /**
     * Cancel Transaction, allow for Pending, On Hold and Completed transaction
     * only cancel transaction with amount > 0
     * Cancel mean that similar as we do not have this transaction
     *
     * @return $this|boolean
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function cancelTransaction()
    {
        if (!$this->getId()
            || !$this->getCustomerId()
            || !$this->getRewardId()
            || $this->getPointAmount() <= 0
            || $this->getStatus() > self::STATUS_COMPLETED
            || !$this->getStatus()
        ) {
            $this->messageManager->addError(__('Invalid transaction data to cancel.'));
            return false;
        }

        // dispatch event when complete a transaction
        $this->_eventManager->dispatch(
            $this->_eventPrefix . '_cancel_' . $this->getData('action'),
            $this->_getEventData()
        );

        if ($this->getStatus() != self::STATUS_COMPLETED) {
            if ($this->getData('status') == self::STATUS_ON_HOLD) {
                $rewardAccount = $this->getRewardAccount();
                $rewardAccount->setHoldingBalance($rewardAccount->getHoldingBalance() - $this->getRealPoint());
                $rewardAccount->save();
            }
            $this->setStatus(self::STATUS_CANCELED);
            $this->save();
            return $this;
        }
        $this->setStatus(self::STATUS_CANCELED);
        $rewardAccount = $this->getRewardAccount();
        if ($rewardAccount->getPointBalance() < $this->getRealPoint()) {
            $this->messageManager->addWarning(__('Account balance is not enough to cancel.'));
            return false;
        }
        $rewardAccount->setPointBalance($rewardAccount->getPointBalance() - $this->getRealPoint());
        $this->sendUpdateBalanceEmail($rewardAccount);

        // Save reward account and transaction to database
        $rewardAccount->save();
        $this->save();

        // Change point used for other transaction
        if ($this->getPointUsed() > 0) {
            $pointAmount = $this->getPointAmount();
            $this->setPointAmount(-$this->getPointUsed());
            $this->_getResource()->updatePointUsed($this);
            $this->setPointAmount($pointAmount);
        }

        return $this;
    }

    /**
     * Expire Transaction, allow for Pending, On Hold and Completed transaction
     *
     * Only expire transaction with amount > 0
     *
     * @return $this|boolean
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function expireTransaction()
    {
        if (!$this->getId()
            || !$this->getCustomerId()
            || !$this->getRewardId()
            || $this->getPointAmount() <= $this->getPointUsed()
            || $this->getStatus() > self::STATUS_COMPLETED
            || !$this->getStatus()
            || strtotime($this->getExpirationDate()) > time()
            || !$this->getExpirationDate()
        ) {
            $this->messageManager->addError(__('Invalid transaction data to expire.'));
            return false;
        }

        // dispatch event when complete a transaction
        $this->_eventManager->dispatch(
            $this->_eventPrefix . '_expire_' . $this->getData('action'),
            $this->_getEventData()
        );

        if ($this->getStatus() != self::STATUS_COMPLETED) {
            if ($this->getData('status') == self::STATUS_ON_HOLD) {
                $rewardAccount = $this->getRewardAccount();
                $rewardAccount->setHoldingBalance($rewardAccount->getHoldingBalance() - $this->getRealPoint());
                $rewardAccount->save();
            }
            $this->setStatus(self::STATUS_EXPIRED);
            $this->save();
            return $this;
        }

        $this->setStatus(self::STATUS_EXPIRED);
        $rewardAccount = $this->getRewardAccount();
        $rewardAccount->setPointBalance(
            $rewardAccount->getPointBalance() - $this->getPointAmount() + $this->getPointUsed()
        );
        $this->sendUpdateBalanceEmail($rewardAccount);

        // Save reward account and transaction to database
        $rewardAccount->save();
        $this->save();
        return $this;
    }

    /**
     * Validate transaction data and create transaction
     *
     * @param array $data
     * @return \Magestore\RewardPoints\Model\Transaction
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function createTransaction($data = [])
    {
        $this->addData($data);

        if (!$this->getPointAmount()) {
            // Don't create transaction without point amount
            return $this;
        }
        if ($this->getCustomer()) {
            $rewardAccount = $this->_rewardpointsHelperCustomer->getAccountByCustomer($this->getCustomer());
        } else {
            $rewardAccount = $this->_rewardpointsHelperCustomer->getAccountByCustomerId($this->getCustomerId());
        }

        if (!$rewardAccount->getId()) {
            $rewardAccount->setCustomerId($this->getCustomerId())
                ->setData('point_balance', 0)
                ->setData('holding_balance', 0)
                ->setData('spent_balance', 0)
                ->setData('is_notification', 1)
                ->setData('expire_notification', 1)
                ->save();
        }

        if ($rewardAccount->getPointBalance() + $this->getPointAmount() < 0) {
            $this->setPointAmount(-$rewardAccount->getPointBalance());
        }

        $this->setData('reward_id', $rewardAccount->getId());
        $this->setData('point_used', 0);

        // Always complete reduce transaction when created
        if ($this->getPointAmount() < 0) {
            if (!$this->getData('status')) {
                $this->setData('status', self::STATUS_COMPLETED);
            }
        } else {
            $this->setData('real_point', $this->getPointAmount());
        }

        // If not set status, set it to Pending
        if (!$this->getData('status')) {
            $this->setData('status', self::STATUS_PENDING);
        }

        // Holding transaction, add holding balance
        if ($this->getData('status') == self::STATUS_ON_HOLD) {
            $rewardAccount->setHoldingBalance($rewardAccount->getHoldingBalance() + $this->getPointAmount());
        }
        // Transaction is spending, add spent balance
        if ($this->getData('action_type') == self::ACTION_TYPE_SPEND) {
            $rewardAccount->setSpentBalance($rewardAccount->getSpentBalance() - $this->getPointAmount());
        }

        // Completed when create transaction
        if ($this->getData('status') == self::STATUS_COMPLETED) {
            //$maxBalance 500
            //$this->getPointAmount() 600
            //$rewardAccount->getPointBalance() 500
            $maxBalance = $this->helper->getConfig(self::XML_PATH_MAX_BALANCE, $this->getStoreId());

            if ($maxBalance > 0
                && $this->getPointAmount() > 0
                && $rewardAccount->getPointBalance() + $this->getPointAmount() > $maxBalance
            ) {
                if ($maxBalance > $rewardAccount->getPointBalance()) {
                    $this->setPointAmount($maxBalance - $rewardAccount->getPointBalance());
                    $this->setRealPoint($maxBalance - $rewardAccount->getPointBalance());
                    $rewardAccount->setPointBalance($maxBalance);
                    $rewardAccount->save();
                    $this->save();
                    $this->sendUpdateBalanceEmail($rewardAccount);
                } else {
                    return $this;
                }
            } else {
                $rewardAccount->setPointBalance($rewardAccount->getPointBalance() + $this->getPointAmount());
                $rewardAccount->save();
                $this->save();
                $this->sendUpdateBalanceEmail($rewardAccount);
            }
        } else {
            if ($this->getPointAmount() < 0
                && $this->getData('status') == self::STATUS_ON_HOLD
                && $this->getData('action_type') == self::ACTION_TYPE_EARN
            ) {
                $isHoldingStatus = true;
                $this->setData('status', self::STATUS_COMPLETED);
                // Update real points and point used for holding transaction (earning) depend on account/ order
                $this->_getResource()->updateRealPointHolding($this);
            }
            $rewardAccount->save();
            $this->save();
        }

        // Save transactions and customer to Database
        if ($this->getPointAmount() < 0 && empty($isHoldingStatus)) {
            if ($this->getData('action_type') == self::ACTION_TYPE_EARN) {
                // Update real points for transaction depend on account/ order
                $this->_getResource()->updateRealPoint($this);
            }
            // Update other transactions (point_used) depend on Account
            $this->_getResource()->updatePointUsed($this);
        }

        // Dispatch Event when create an action
        $this->_eventManager->dispatch(
            $this->_eventPrefix . '_created_' . $this->getData('action'),
            $this->_getEventData()
        );

        return $this;
    }

    /**
     * Get transaction title as HTML
     *
     * @return string
     */
    public function getTitleHtml()
    {

        if ($this->hasData('title') && $this->getData('title') != '') {
            return $this->getData('title');
        }
        try {
            $this->setData('title_html', $this->getActionInstance()->getActionLabel());
        } catch (\Exception $e) {
            $this->setData('title_html', $this->getTitle());
        }
        return $this->getData('title_html');
    }

    /**
     * Get action model of current transaction
     *
     * @return \Magestore\Rewardpoints\Model\InterfaceAction
     */
    public function getActionInstance()
    {
        return $this->_helperAction->getActionModel($this->getAction());
    }

    /**
     * @inheritDoc
     */
    public function getTransactionId()
    {
        return $this->getData('transaction_id');
    }

    /**
     * @inheritDoc
     */
    public function getRewardId()
    {
        return $this->getData('reward_id');
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }

    /**
     * @inheritDoc
     */
    public function getCustomerEmail()
    {
        return $this->getData('customer_email');
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPointBalance()
    {
        return $this->getRewardAccount()->getPointBalance();
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * @inheritDoc
     */
    public function getAction()
    {
        return $this->getData('action');
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    /**
     * @inheritDoc
     */
    public function getPointAmount()
    {
        return $this->getData('point_amount');
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * @inheritDoc
     */
    public function getCreatedTime()
    {
        return $this->getData('created_time');
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedTime()
    {
        return $this->getData('updated_time');
    }

    /**
     * @inheritDoc
     */
    public function getExpirationDate()
    {
        return $this->getData('expiration_date');
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData('order_id');
    }

    /**
     * @inheritDoc
     */
    public function getOrderIncrementId()
    {
        return $this->getData('order_increment_id');
    }

    /**
     * @inheritDoc
     */
    public function getOrderAmount()
    {
        return $this->getData('order_amount');
    }

    /**
     * @inheritDoc
     */
    public function getDiscount()
    {
        return $this->getData('discount');
    }

    /**
     * @inheritDoc
     */
    public function getExtraContent()
    {
        return $this->getData('extra_content');
    }

    /**
     * @inheritDoc
     */
    public function setTransactionId($transactionId)
    {
        return $this->setData('transaction_id', $transactionId);
    }

    /**
     * @inheritDoc
     */
    public function setRewardId($rewardId)
    {
        return $this->setData('reward_id', $rewardId);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData('customer_id', $customerId);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData('customer_email', $customerEmail);
    }

    /**
     * @inheritDoc
     */
    public function setCurrentPointBalance($balance)
    {
        return $this->setData('current_point_balance', $balance);
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        return $this->setData('title', $title);
    }

    /**
     * @inheritDoc
     */
    public function setAction($action)
    {
        return $this->setData('action', $action);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
    }

    /**
     * @inheritDoc
     */
    public function setPointAmount($pointAmount)
    {
        return $this->setData('point_amount', $pointAmount);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData('status', $status);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedTime($createdTime)
    {
        return $this->setData('created_time', $createdTime);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedTime($time)
    {
        return $this->setData('updated_time', $time);
    }

    /**
     * @inheritDoc
     */
    public function setExpirationDate($time)
    {
        return $this->setData('expiration_date', $time);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($id)
    {
        return $this->setData('order_id', $id);
    }

    /**
     * @inheritDoc
     */
    public function setOrderIncrementId($id)
    {
        return $this->setData('order_increment_id', $id);
    }

    /**
     * @inheritDoc
     */
    public function setOrderAmount($amount)
    {
        return $this->setData('order_amount', $amount);
    }

    /**
     * @inheritDoc
     */
    public function setDiscount($discount)
    {
        return $this->setData('discount', $discount);
    }

    /**
     * @inheritDoc
     */
    public function setExtraContent($content)
    {
        return $this->setData('content', $content);
    }
}
