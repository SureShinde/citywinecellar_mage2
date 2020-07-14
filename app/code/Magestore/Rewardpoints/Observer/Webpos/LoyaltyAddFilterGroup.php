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
 * @package     Magestore_OneStepCheckout
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Rewardpoints\Observer\Webpos;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class ProductGetFinalPrice
 * @package Magestore\Affiliateplus\Observer
 */
class LoyaltyAddFilterGroup implements ObserverInterface
{
    protected $moduleManager;

    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->moduleManager = $moduleManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $updatedAt = $observer->getEvent()->getUpdatedAt();
        $checkProcess = $observer->getEvent()->getCheckProcess();
        if ($updatedAt) {
            if (!$this->isStoreCreditEnable()) {
                $collection->getSelect()->where('
                    rewardpoints_customer.updated_at >= "' . $updatedAt . '"
                ');
            } else {
                $collection->getSelect()->where('
                    rewardpoints_customer.updated_at >= "' . $updatedAt . '" OR
                    customer_credit.updated_at >= "' . $updatedAt . '"
                ');
                $checkProcess->setData('process_store_credit', true);
            }
        }
        return;
    }

    /**
     * @return bool
     */
    public function isStoreCreditEnable()
    {
        if ($this->moduleManager->isEnabled('Magestore_Customercredit')) {
            if ($this->scopeConfig->getValue('customercredit/general/enable')) {
                return true;
            }
        }
        return false;
    }
}
