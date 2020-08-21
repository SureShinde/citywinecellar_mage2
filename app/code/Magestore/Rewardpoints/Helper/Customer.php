<?php

namespace Magestore\Rewardpoints\Helper;

/**
 * Class Customer
 *
 * Customer helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Customer extends Config
{
    /**
     * reward account model Factory
     *
     * @var \Magestore\Rewardpoints\Model\CustomerFactory
     */
    protected $_rewardAccountFactory = null;

    /**
     * reward account model
     *
     * @var \Magestore\Rewardpoints\Model\Customer
     */
    protected $rewardAccount = null;

    /**
     * current customer ID
     *
     * @var int
     */
    protected $_customerId = null;

    /**
     * current customer
     */
    protected $_customer = null;

    /**
     * current working store ID
     *
     * @var int
     */
    protected $_storeId = null;

    /**
     * get current customer model
     *
     * @return \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory = null;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $_customerSessionFactory;

    /**
     * @var \Magento\Backend\Model\Session\QuoteFactory
     */
    protected $_adminQuoteSessionFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Config
     */
    protected $helper;

    /**
     * @var Point
     */
    protected $pointHelper;

    /**
     * @var Calculation\Spending
     */
    protected $spendingHelper;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    const XML_PATH_DISPLAY_TOPLINK = 'rewardpoints/display/toplink';
    const XML_PATH_REDEEMABLE_POINTS = 'rewardpoints/spending/redeemable_points';

    /**
     * Customer constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Magento\Backend\Model\Session\QuoteFactory $adminQuoteSessionFactory
     * @param \Magestore\Rewardpoints\Model\CustomerFactory $rewardCustomerFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Config $globalConfig
     * @param Point $point
     * @param \Magento\Framework\App\State $appState
     * @param Calculation\Spending $spending
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Backend\Model\Session\QuoteFactory $adminQuoteSessionFactory,
        \Magestore\Rewardpoints\Model\CustomerFactory $rewardCustomerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Rewardpoints\Helper\Config $globalConfig,
        \Magestore\Rewardpoints\Helper\Point $point,
        \Magento\Framework\App\State $appState,
        \Magestore\Rewardpoints\Helper\Calculation\Spending $spending
    ) {
        parent::__construct($context);
        $this->_customerSessionFactory = $customerSessionFactory;
        $this->_adminQuoteSessionFactory = $adminQuoteSessionFactory;
        $this->_rewardAccountFactory = $rewardCustomerFactory;
        $this->_storeManager = $storeManager;
        $this->helper = $globalConfig;
        $this->_appState = $appState;
        $this->pointHelper = $point;
        $this->spendingHelper = $spending;
    }

    /**
     * Get customer
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomer()
    {
        if ($this->_storeManager->getStore()->getCode() == \Magento\Store\Model\Store::ADMIN_CODE) {
            $this->_customer = $this->_adminQuoteSessionFactory->create()->getCustomer();
            return $this->_customer;
        }
        if ($this->_customerSessionFactory->create()->getCustomerId()) {
            $this->_customer = $this->_customerSessionFactory->create()->getCustomer();
            return $this->_customer;
        }
        $sesion = \Magento\Framework\App\ObjectManager::getInstance()->create(
            \Magento\Checkout\Model\Session::class
        );
        $quoteId = $sesion->getQuoteId();
        if ($quoteId) {
            $quote = $sesion->getQuote();
            $customerId = $quote->getCustomerId();
            if ($customerId) {
                $this->_customer = \Magento\Framework\App\ObjectManager::getInstance()->create(
                    \Magento\Customer\Api\CustomerRepositoryInterface::class
                )->getById($customerId);
            }
        }
        return $this->_customer;
    }

    /**
     * Get current customer ID
     *
     * @return int
     */
    public function getCustomerId()
    {
        if ($this->_customerId === null) {
            $customerId = 0;
            if ($this->_appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
                $this->_customerId = $this->_adminQuoteSessionFactory->create()->getCustomerId();
                return $this->_customerId;
            } else {
                if ($this->_customerSessionFactory->create()->isLoggedIn()) {
                    $customerId = $this->_customerSessionFactory->create()->getCustomerId();
                }
            }
            if ($customerId) {
                $this->_customerId = $customerId;
            } else {
                $this->_customerId = 0;
            }
        }
        return $this->_customerId;
    }

    /**
     * Get current working store id, used when checkout
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            if ($this->_storeManager->isSingleStoreMode()) {
                $this->_storeId = $this->_storeManager->getStore()->getId();
            } else {
                if ($this->_appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
                    $this->_storeId = $this->_adminQuoteSessionFactory->create()->getStoreId();
                } else {
                    $this->_storeId = $this->_storeManager->getStore()->getId();
                }
            }
        }
        return $this->_storeId;
    }

    /**
     * Get current reward points customer account
     *
     * @return \Magestore\Rewardpoints\Model\Customer
     */
    public function getAccount()
    {
        if (!$this->rewardAccount) {
            $rewardAccount = $this->_rewardAccountFactory->create();
            if ($this->getCustomerId()) {
                $rewardAccount->load($this->getCustomerId(), 'customer_id');
                $rewardAccount->setData('customer', $this->getCustomer());
            }
            $sesion = \Magento\Framework\App\ObjectManager::getInstance()->create(
                \Magento\Checkout\Model\Session::class
            );
            $quoteId = $sesion->getQuoteId();
            if ($quoteId) {
                $quote = \Magento\Framework\App\ObjectManager::getInstance()->get(
                    \Magento\Quote\Api\CartRepositoryInterface::class
                )->get($quoteId);
                $customerId = $quote->getCustomerId();
                if ($customerId) {
                    $rewardAccount->load($customerId, 'customer_id');
                    $rewardAccount->setData('customer', $this->getCustomer());
                }
            }
            $this->rewardAccount = $rewardAccount;
        }
        return $this->rewardAccount;
    }

    /**
     * Get Reward Points Account by Customer
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return \Magestore\Rewardpoints\Model\Customer
     */
    public function getAccountByCustomer($customer)
    {
        $rewardAccount = $this->getAccountByCustomerId($customer->getId());
        if (!$rewardAccount->hasData('customer')) {
            $rewardAccount->setData('customer', $customer);
        }
        return $rewardAccount;
    }

    /**
     * Get Reward Points Account by Customer ID
     *
     * @param int $customerId
     * @return \Magestore\Rewardpoints\Model\Customer
     */
    public function getAccountByCustomerId($customerId = null)
    {
        if (empty($customerId) || $customerId == $this->getCustomerId()
        ) {
            return $this->getAccount();
        }
        return $this->_rewardAccountFactory->create()->load($customerId, 'customer_id');
    }

    /**
     * Get reward points balance of current customer
     *
     * @return int
     */
    public function getBalance()
    {
        return $this->getAccount()->getPointBalance();
    }

    /**
     * Get string of points balance formated
     *
     * @return string
     */
    public function getBalanceFormated()
    {
        return $this->pointHelper->format(
            $this->getBalance(),
            $this->getStoreId()
        );
    }

    /**
     * Get string of points balance formated
     *
     * Balance is estimated after customer use point to spent
     *
     * @return string
     */
    public function getBalanceAfterSpentFormated()
    {
        return $this->pointHelper->format(
            $this->getBalance() - $this->spendingHelper->getTotalPointSpent(),
            $this->getStoreId()
        );
    }

    /**
     * Check show customer reward points on top link
     *
     * @param type $store
     * @return boolean
     */
    public function showOnToplink($store = null)
    {
        return $this->helper->getConfig(self::XML_PATH_DISPLAY_TOPLINK, $store);
    }

    /**
     * Check customer can use point to spend for order or not
     *
     * @param type $store
     * @return boolean
     */
    public function isAllowSpend($store = null)
    {
        $minPoint = (int)$this->getSpendingConfig('redeemable_points', $store);
        if ($minPoint > $this->getBalance()) {
            return false;
        }
        return true;
    }
}
