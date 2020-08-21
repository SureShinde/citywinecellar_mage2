<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;

/**
 * Source option Payment
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Payment implements \Magento\Framework\Option\ArrayInterface
{
    const EVENT_WEBPOS_GET_PAYMENT_OPTION_ARRAY_AFTER = 'webpos_get_payment_option_array_after';
    const EVENT_WEBPOS_TO_PAYMENT_OPTION_ARRAY_AFTER = 'webpos_to_payment_option_array_after';
    const TYPE_CREDIT_CARD = 1;
    const TYPE_OFFLINE_PAYMENT = 0;
    const SPECIFIC_PAYMENT = 'webpos/payment/method';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Webpos payment model
     *
     * @var \Magestore\Webpos\Model\Payment\Payment
     */
    protected $paymentModelFactory;

    /**
     * Allow payments array
     *
     * @var array
     */
    protected $_allowPayments;

    /**
     * Payment config model
     *
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfigModel;

    /**
     * Payment config model
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_corePaymentHelper;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;
    /**
     * @var string[]
     */
    protected $_ccPayments;
    /**
     * @var string[]
     */
    protected $_offlinePayments;

    /**
     * Payment constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magestore\Webpos\Api\Data\Payment\PaymentInterfaceFactory $paymentModel
     * @param \Magento\Payment\Model\Config $paymentConfigModel
     * @param \Magento\Payment\Helper\Data $corePaymentHelper
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magestore\Webpos\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\Webpos\Api\Data\Payment\PaymentInterfaceFactory $paymentModel,
        \Magento\Payment\Model\Config $paymentConfigModel,
        \Magento\Payment\Helper\Data $corePaymentHelper,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magestore\Webpos\Helper\Data $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->paymentModelFactory = $paymentModel;
        $this->_paymentConfigModel = $paymentConfigModel;
        $this->_corePaymentHelper = $corePaymentHelper;
        $this->eventManager = $eventManager;
        $this->helper = $helper;
        $this->_allowPayments = [
            'cashforpos',
            'ccforpos',
            'paypal_direct',
            'authorizenet_directpost',
            'cryozonic_stripe',
            'payflowpro_integration',
            'paynl_payment_instore'
        ];
        $this->_ccPayments = [
            'authorizenet_directpost',
            'cryozonic_stripe',
            'payflowpro_integration',
            'paynl_payment_instore'
        ];
        $this->_offlinePayments = [
            'cashforpos',
            'codforpos',
            'ccforpos'
        ];
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->_paymentConfigModel->getActiveMethods();
        $storeMethods = $this->_corePaymentHelper->getStoreMethods();
        $ignores = [];
        $options = [];
        $ignores = $this->addPaymentsOptions($options, $collection, $ignores, true);
        $this->addPaymentsOptions($options, $storeMethods, $ignores, true);

        $data = ['payments' => new \Magento\Framework\DataObject(['options' => $options])];
        $this->eventManager->dispatch(
            self::EVENT_WEBPOS_TO_PAYMENT_OPTION_ARRAY_AFTER,
            $data
        );
        $options = $data['payments']->getOptions();

        return $options;
    }

    /**
     * Get option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $collection = $this->_paymentConfigModel->getActiveMethods();
        $storeMethods = $this->_corePaymentHelper->getStoreMethods();
        $ignores = [];
        $options = [0 => __('Please select a payment')];
        $ignores = $this->addPaymentsOptions($options, $collection, $ignores);
        $ignores = $this->addPaymentsOptions($options, $storeMethods, $ignores);
        // check payment method payflowpro
        try {
            $payflowproIntegrationPayment = $this->_corePaymentHelper->getMethodInstance('payflowpro_integration');
            if ($payflowproIntegrationPayment->isActiveWebpos()) {
                $payment = [$payflowproIntegrationPayment];
                $this->addPaymentsOptions($options, $payment, $ignores, true);
            }
        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Psr\Log\LoggerInterface::class)
                ->info($e->getMessage());
        }
        $data = ['payments' => new \Magento\Framework\DataObject(['options' => $options])];
        $this->eventManager->dispatch(
            self::EVENT_WEBPOS_GET_PAYMENT_OPTION_ARRAY_AFTER,
            $data
        );
        $options = $data['payments']->getOptions();

        return $options;
    }

    /**
     * Get Pos Payment Methods
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPosPaymentMethods()
    {
        $paymentList = $this->getActiveMethods();
        return $paymentList;
    }

    /**
     * Retrieve active system payments
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getActiveMethods()
    {
        $list = [];
        $webposPayment = $this->scopeConfig->getValue(
            'webpos/payment',
            ScopeInterface::SCOPE_STORES,
            $this->helper->getCurrentStoreView()->getId()
        );
        foreach ($webposPayment as $code => $data) {
            if (!in_array($code, $this->_offlinePayments)) {
                continue;
            }
            if (isset($data['active'], $data['model']) && (bool)$data['active']) {
                /** @var MethodInterface $methodModel Actually it's wrong interface */
                if ($code == $this->getDefaultPaymentMethod()) {
                    $isDefault = 1;
                } else {
                    $isDefault = 0;
                }

                if (isset($data['use_reference_number']) && $data['use_reference_number']) {
                    $isReferenceNumber = 1;
                } else {
                    $isReferenceNumber = 0;
                }

                if (isset($data['pay_later']) && $data['pay_later']) {
                    $isPayLater = 1;
                } else {
                    $isPayLater = 0;
                }

                if (isset($data['is_suggest_money']) && $data['is_suggest_money']) {
                    $isSuggestMoney = 1;
                } else {
                    $isSuggestMoney = 0;
                }

                if (isset($data['can_due']) && $data['can_due']) {
                    $canDue = 1;
                } else {
                    $canDue = 0;
                }
                $sortOrder = isset($data['sort_order']) ? (int)$data['sort_order'] : 0;
                $paymentModel = $this->paymentModelFactory->create();
                $paymentModel->setCode($code);
                $paymentModel->setTitle(isset($data['title']) ? $data['title'] : '');
                $paymentModel->setType(self::TYPE_OFFLINE_PAYMENT);
                $paymentModel->setIsDefault($isDefault);
                $paymentModel->setIsReferenceNumber($isReferenceNumber);
                $paymentModel->setIsPayLater($isPayLater);
                $paymentModel->setIsSuggestMoney($isSuggestMoney);
                $paymentModel->setCanDue($canDue);
                $paymentModel->setSortOrder($sortOrder);
                if (in_array($code, $this->_ccPayments)) {
                    $paymentModel->setType(self::TYPE_CREDIT_CARD);
                }
                $list[] = $paymentModel->getData();
            }
        }
        return $list;
    }

    /**
     * Get array of allow payment methods
     *
     * @return array
     */
    public function getAllowPaymentMethods()
    {
        return $this->_allowPayments;
    }

    /**
     * Add Payments Options
     *
     * @param array $list
     * @param array $collection
     * @param array $ignores
     * @param bool $widthLabel
     * @return array
     */
    public function addPaymentsOptions(&$list, $collection, $ignores, $widthLabel = false)
    {
        $addedMethods = [];
        if (count($collection) > 0) {
            foreach ($collection as $item) {
                if (in_array($item->getCode(), $ignores)
                    || !in_array($item->getCode(), $this->_allowPayments)
                    || $item->getCode() == 'cashforpos') {
                    continue;
                }
                $title = $item->getTitle() ? $item->getTitle() : $item->getCode();
                if ($widthLabel) {
                    $list[] = ['value' => $item->getCode(), 'label' => $title];
                } else {
                    $list["'" . $item->getCode() . "'"] = $title;
                }
                $addedMethods[] = $item->getCode();
            }
        }
        return $addedMethods;
    }

    /**
     * Add Pos Payments
     *
     * @param array $list
     * @param array $collection
     * @param array $ignores
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function addPosPayments(&$list, $collection, $ignores)
    {
        $addedMethods = [];
        if (count($collection) > 0) {
            foreach ($collection as $item) {
                if (in_array($item->getCode(), $ignores)
                    || !in_array($item->getCode(), $this->_allowPayments)
                    || !$this->isAllowOnWebPOS($item->getCode())) {
                    continue;
                }

                if ($item->getCode() == $this->getDefaultPaymentMethod()) {
                    $isDefault = 1;
                } else {
                    $isDefault = 0;
                }

                if ($item->getConfigData('use_reference_number')) {
                    $isReferenceNumber = 1;
                } else {
                    $isReferenceNumber = 0;
                }

                if ($item->getConfigData('pay_later')) {
                    $isPayLater = 1;
                } else {
                    $isPayLater = 0;
                }

                if ($item->getConfigData('is_suggest_money')) {
                    $isSuggestMoney = 1;
                } else {
                    $isSuggestMoney = 0;
                }

                if ($item->getConfigData('can_due')) {
                    $canDue = 1;
                } else {
                    $canDue = 0;
                }

                $paymentModel = $this->paymentModelFactory->create();
                $paymentModel->setCode($item->getCode());
                $paymentModel->setTitle($item->getTitle());
                $paymentModel->setType(self::TYPE_OFFLINE_PAYMENT);
                $paymentModel->setIsDefault($isDefault);
                $paymentModel->setIsReferenceNumber($isReferenceNumber);
                $paymentModel->setIsPayLater($isPayLater);
                $paymentModel->setIsSuggestMoney($isSuggestMoney);
                $paymentModel->setCanDue($canDue);
                if (in_array($item->getCode(), $this->_ccPayments)) {
                    $paymentModel->setType(self::TYPE_CREDIT_CARD);
                }
                $list[] = $paymentModel->getData();
                $addedMethods[] = $item->getCode();
            }
        }
        return $addedMethods;
    }

    /**
     * Is Allow On WebPOS
     *
     * @param string $code
     * @return boolean
     */
    public function isAllowOnWebPOS($code)
    {
        $specificPayment = $this->scopeConfig->getValue(
            self::SPECIFIC_PAYMENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $specificPayment = explode(',', $specificPayment);
        $specificPayment[] = 'cashforpos';
        if (in_array($code, $specificPayment)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Default Payment Method
     *
     * @return string
     */
    public function getDefaultPaymentMethod()
    {
        return '';
    }
}
