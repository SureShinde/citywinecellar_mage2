<?php

namespace Magestore\Rewardpoints\Controller;

use Magento\Framework\App\RequestInterface;

/**
 * Class AbstractAction
 * @package Magestore\Rewardpoints\Controller
 */

abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    /**
     * @var \Magestore\Rewardpoints\Helper\Data
     */
    protected $_helperData;
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $_customerSessionFactory;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magestore\Rewardpoints\Helper\Config
     */
    protected $_helperConfig;
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_modelPage;

    /**
     * @var \Magestore\Rewardpoints\Helper\Block\Spend
     */
    protected $_helperSpend;

    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $_checkoutSessionFactory;
    /**
     * @var \Magestore\Rewardpoints\Block\Checkout\Form
     */
    protected $_checkoutForm;
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_checkoutCart;
    /**
     * @var \Magestore\Rewardpoints\Helper\Point
     */
    protected $_helperPoint;
    /**
     * @var \Magestore\Rewardpoints\Helper\Calculation\Spending
     */
    protected $_calculationSpending;

    /**
     * @var \Magestore\Rewardpoints\Model\CustomerFactory
     */
    protected $_rewardpointsCustomerFactory;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $_sessionManager;

    /**
     * AbstractAction constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magestore\Rewardpoints\Helper\Data $helperData
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Rewardpoints\Helper\Config $helperConfig
     * @param \Magento\Cms\Model\PageFactory $modelPageFactory
     * @param \Magestore\Rewardpoints\Helper\Block\Spend $helperSpend
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory
     * @param \Magestore\Rewardpoints\Block\Checkout\Form $checkoutForm
     * @param \Magento\Checkout\Model\CartFactory $checkoutCartFactory
     * @param \Magestore\Rewardpoints\Helper\Point $helperPoint
     * @param \Magestore\Rewardpoints\Model\CustomerFactory $rewardpointsCustomerFactory
     * @param \Magestore\Rewardpoints\Helper\Calculation\Spending $calculationSpending
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magestore\Rewardpoints\Helper\Data $helperData,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Rewardpoints\Helper\Config $helperConfig,
        \Magento\Cms\Model\PageFactory $modelPageFactory,
        \Magestore\Rewardpoints\Helper\Block\Spend $helperSpend,
        \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory,
        \Magestore\Rewardpoints\Block\Checkout\Form $checkoutForm,
        \Magento\Checkout\Model\CartFactory $checkoutCartFactory,
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        \Magestore\Rewardpoints\Model\CustomerFactory $rewardpointsCustomerFactory,
        \Magestore\Rewardpoints\Helper\Calculation\Spending $calculationSpending,
        \Magento\Framework\Session\SessionManager $sessionManager
    ){
        $this->_request = $request;
        $this->_rewardpointsCustomerFactory = $rewardpointsCustomerFactory;
        $this->_helperData = $helperData;
        $this->_customerSessionFactory = $customerSessionFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_storeManager = $storeManager;
        $this->_helperConfig = $helperConfig;
        $this->_modelPageFactory = $modelPageFactory;
        $this->_helperSpend = $helperSpend;
        $this->_checkoutSessionFactory = $checkoutSessionFactory;
        $this->_checkoutForm = $checkoutForm;
        $this->_checkoutCartFactory = $checkoutCartFactory;
        $this->_helperPoint = $helperPoint;
        $this->_calculationSpending = $calculationSpending;
        $this->_sessionManager = $sessionManager;
        parent::__construct($context);
    }

    /**
     * @return $this
     */

    public function dispatch(RequestInterface $request) {
        if (!$this->_helperData->isEnable()) {
            $this->_redirect('customer/account');
            $this->_actionFlag->set('',\Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
        }
        $action = $this->getRequest()->getActionName();
        if ($action != 'policy' && $action != 'redirectLogin') {
            // Check customer authentication
            if (!$this->_customerSessionFactory->create()->isLoggedIn()) {
                $this->_customerSessionFactory->create()->setAfterAuthUrl(
                    $this->_url->getUrl($this->_request->getFullActionName('/'))
                );
                $this->_redirect('customer/account/login');
                $this->_actionFlag->set('',\Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            }
        }
        return parent::dispatch($request);
    }

}