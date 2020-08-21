<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Rewardpoints\Helper;

/**
 * RewardPoints Action Library Helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Action extends Config
{

    const XML_CONFIG_ACTIONS    = 'global/rewardpoints/actions';

    /**
     * reward points actions config
     *
     * @var array
     */
    protected $_config = [];

    /**
     * Actions Array (code => label)
     *
     * @var array
     */
    protected $_actions = null;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Action constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $actionConfig = [
            // Admin - Change by Custom
            "admin" => \Magestore\Rewardpoints\Model\Action\Admin::class,
            // Sales - Earning Actions
            "earning_invoice" => \Magestore\Rewardpoints\Model\Action\Earning\Invoice::class,
            "earning_creditmemo" => \Magestore\Rewardpoints\Model\Action\Earning\Creditmemo::class,
            "earning_cancel" => \Magestore\Rewardpoints\Model\Action\Earning\Cancel::class,
            // Sales - Spending Actions
            "spending_order" => \Magestore\Rewardpoints\Model\Action\Spending\Order::class,
            "spending_creditmemo" => \Magestore\Rewardpoints\Model\Action\Spending\Creditmemo::class,
            "spending_cancel" => \Magestore\Rewardpoints\Model\Action\Spending\Cancel::class,
        ];
        $this->_eventManager->dispatch(
            'action_config_rewardpoints',
            ['object'=>$this]
        );
        foreach ($actionConfig as $code => $model) {
            $this->_config[$code] = (string)$model;
        }
        $this->messageManager = $messageManager;
    }
    
    /**
     * Add action config
     *
     * @param array $configs
     * @return $this
     */
    public function setActionConfig($configs = [])
    {
        foreach ($configs as $code => $model) {
            $this->_config[$code] = (string)$model;
        }
        return $this;
    }
    
    /**
     * Add transaction that change customer reward point balance
     *
     * @param string $actionCode
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Framework\DataObject|null $object
     * @param array $extraContent
     * @return \Magestore\Rewardpoints\Model\Transaction
     */
    public function addTransaction($actionCode, $customer, $object = null, $extraContent = [])
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->create(\Magento\Store\Model\StoreManagerInterface::class);
//        \Varien_Profiler::start('REWARDPOINTS_HELPER_ACTION::addTransaction');
        if (!$customer->getId()) {
            $this->messageManager->addError(
                __('Customer must be existed.')
            );

        }
        /** @var \Magestore\Rewardpoints\Model\Action\InterfaceAction $actionModel */
        $actionModel = $this->getActionModel($actionCode);
        $actionModel->setData([
            'customer'      => $customer,
            'action_object' => $object,
            'extra_content' => $extraContent
        ])->prepareTransaction();

        $transaction = $objectManager->create(\Magestore\Rewardpoints\Model\Transaction::class);
        if (is_array($actionModel->getData('transaction_data'))) {
            $transaction->setData($actionModel->getData('transaction_data'));
        }
        $transaction->setData('point_amount', (int)$actionModel->getPointAmount());

        if (!$transaction->hasData('store_id')) {
            $transaction->setData('store_id', $storeManager->getStore()->getId());
        }

        $transaction->createTransaction([
            'customer_id'   => $customer->getId(),
            'customer'      => $customer,
            'customer_email'=> $customer->getEmail(),
            'title'         => $actionModel->getTitle(),
            'action'        => $actionCode,
            'action_type'   => $actionModel->getActionType(),
            'created_time'  => date('Y-m-d H:i:s'),
            'updated_time'  => date('Y-m-d H:i:s'),
        ]);

//        Varien_Profiler::stop('REWARDPOINTS_HELPER_ACTION::addTransaction');
        return $transaction;
    }

    /**
     * Get Class Model for Action by code
     *
     * @param string $code
     * @return string|null
     * @throws \Exception
     */
    public function getActionModelClass($code)
    {
        if (isset($this->_config[$code]) && $this->_config[$code]) {
            return $this->_config[$code];
        }
        $this->messageManager->addError(
            __('Action code %1 not found on config.', $code)
        );
        return null;
    }

    /**
     * Get action Model by Code
     *
     * @param string $code
     * @return \Magestore\Rewardpoints\Model\Action\InterfaceAction
     * @throws \Exception
     */
    public function getActionModel($code)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $modelClass = $this->getActionModelClass($code);
        $model = $objectManager->create($modelClass);
        if (!$model->getCode()) {
            $model->setCode($code);
        }
        return $model;
    }

    /**
     * Get actions hash options
     *
     * @return array
     */
    public function getActionsHash()
    {
        if ($this->_actions === null) {
            $this->_actions = [];
            foreach (array_keys($this->_config) as $code) {
                try {
                    $model = $this->getActionModel($code);
                    $this->_actions[$code] = $model->getActionLabel();
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                }
            }
        }
        return $this->_actions;
    }

    /**
     * Get actions array options
     *
     * @return array
     */
    public function getActionsArray()
    {
        $actions = [];
        foreach ($this->getActionsHash() as $value => $label) {
            $actions[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $actions;
    }
}
