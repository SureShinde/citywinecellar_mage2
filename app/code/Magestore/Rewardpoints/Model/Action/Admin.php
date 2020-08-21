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

namespace Magestore\Rewardpoints\Model\Action;

/**
 * Action change points by admin
 */
class Admin extends \Magestore\Rewardpoints\Model\Action\AbstractAction implements
    \Magestore\Rewardpoints\Model\Action\InterfaceAction
{
    /**
     * @var \Magento\Backend\Model\AuthFactory
     */
    protected $_authFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Admin constructor.
     *
     * @param \Magento\Backend\Model\AuthFactory $authFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\Model\AuthFactory $authFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_authFactory = $authFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function getPointAmount()
    {
        $actionObject = $this->getData('action_object');
        if (!is_object($actionObject)) {
            return 0;
        }
        return (int)$actionObject->getPointAmount();
    }

    /**
     * @inheritDoc
     */
    public function getActionLabel()
    {
        return __('Changed by Admin');
    }

    /**
     * @inheritDoc
     */
    public function getActionType()
    {
        return \Magestore\Rewardpoints\Model\Transaction::ACTION_TYPE_BOTH;
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        $actionObject = $this->getData('action_object');
        if (!is_object($actionObject)) {
            return '';
        }
        return (string)$actionObject->getData('title');
    }

    /**
     * @inheritDoc
     */
    public function getTitleHtml($transaction = null)
    {
        if ($transaction === null) {
            return $this->getTitle();
        }
        if ($this->_storeManager->getStore()->isAdmin()) {
            return '<strong>' . $transaction->getExtraContent() . ': </strong>' . $transaction->getTitle();
        }
        return $transaction->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function prepareTransaction()
    {
        $transactionData = [
            'status'    => \Magestore\Rewardpoints\Model\Transaction::STATUS_COMPLETED,
        ];
        if ($user = $this->_authFactory->create()->getUser()) {
            $transactionData['extra_content'] = $user->getUsername();
        }
        $actionObject = $this->getData('action_object');
        if (is_object($actionObject) && $actionObject->getExpirationDay() && $this->getPointAmount() > 0) {
            $transactionData['expiration_date'] = $this->getExpirationDate($actionObject->getExpirationDay());
        }
        $this->setData('transaction_data', $transactionData);
        return parent::prepareTransaction();
    }
}
