<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Giftvoucher Index Redeem Action
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Redeem extends \Magestore\Giftvoucher\Controller\Action implements HttpPostActionInterface
{

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        if (!$this->customerLoggedIn()) {
            return $this->_redirect("customer/account/login");
        }
        if (!$this->getHelper()->getGeneralConfig('enablecredit')) {
            return $this->_redirect("giftvoucher/index/index");
        }
        $code = $this->getRequest()->getParam('giftvouchercode');

        $max             = $this->getHelper()->getGeneralConfig('maximum');
        $giftCardSession = $this->_objectManager->create(\Magestore\Giftvoucher\Model\Session::class);

        if ($code) {
            $giftVoucher = $this->getModel(\Magestore\Giftvoucher\Model\Giftvoucher::class)->loadByCode($code);

            $codes = $giftCardSession->getCodes();
            if (!$this->getHelper()->isAvailableToAddCode()) {
                $this->messageManager->addError(__('The maximum number of times to enter gift codes is %1!', $max));
                return $this->_redirect("giftvoucher/index/index");
            }
            if (!$giftVoucher->getId()) {
                $codes[] = $code;
                $codes   = array_unique($codes);
                $giftCardSession->setCodes($codes);
                $errorMessage = __('Gift card "%1" is invalid.', $code);
                if ($max) {
                    $errorMessage .= __(
                        'You have %1 time(s) remaining to re-enter Gift Card code.',
                        $max - count($codes)
                    );
                }
                $this->messageManager->addError($errorMessage);
                return $this->_redirect("giftvoucher/index/addredeem");
            } else {
                //Hai.Tran
                $conditions = $giftVoucher->getConditionsSerialized();
                $serializer = $this->_objectManager->get(\Magento\Framework\Serialize\SerializerInterface::class);
                if (!empty($conditions)) {
                    $conditions = $serializer->unserialize($conditions);
                    if (is_array($conditions) && !empty($conditions)) {
                        if (!$this->getHelper()->getGeneralConfig('credit_condition')
                            && isset($conditions['conditions']) && $conditions['conditions']
                        ) {
                            $this->messageManager->addError(__(
                                'Gift code "%1" has usage conditions, you cannot redeem it to Gift Card credit',
                                $code
                            ));
                            return $this->_redirect("giftvoucher/index/addredeem");
                        }
                    }
                }
                $actions = $giftVoucher->getActionsSerialized();
                if (!empty($actions)) {
                    $actions = $serializer->unserialize($actions);
                    if (is_array($actions) && !empty($actions)) {
                        if (!$this->getHelper()->getGeneralConfig('credit_condition')
                            && isset($actions['conditions']) && $actions['conditions']
                        ) {
                            $this->messageManager->addError(__(
                                'Gift code "%1" has usage conditions, you cannot redeem it to Gift Card credit',
                                $code
                            ));
                            return $this->_redirect("giftvoucher/index/addredeem");
                        }
                    }
                }
                if (!$this->getHelper()->canUseCode($giftVoucher)) {
                    $this->messageManager->addError(
                        __('The gift code usage has exceeded the number of users allowed.')
                    );
                    return $this->_redirect("giftvoucher/index/index");
                }
                $customer = $this->getModel(\Magento\Customer\Model\Session::class)->getCustomer();
                if ($giftVoucher->getBalance() == 0) {
                    $this->messageManager->addError(__('%1 - The current balance of this gift code is 0.', $code));
                    return $this->_redirect("giftvoucher/index/addredeem");
                }
                if ($giftVoucher->getStatus() != 2 && $giftVoucher->getStatus() != 4) {
                    $this->messageManager->addError(__('Gift code "%1" is not avaliable', $code));
                    return $this->_redirect("giftvoucher/index/addredeem");
                }
                if ($giftVoucher->getData('set_id')) {
                    $this->messageManager->addError(__('Gift code "%1" is not avaliable', $code));
                    return $this->_redirect("giftvoucher/index/addredeem");
                } else {
                    $balance = $giftVoucher->getBalance();

                    $credit = $this->getModel(\Magestore\Giftvoucher\Model\Credit::class)->getCreditAccountLogin();
                    $creditCurrencyCode = $credit->getCurrency();
                    $baseCurrencyCode   = $this->_storeManager->getStore()->getBaseCurrencyCode();
                    if (!$creditCurrencyCode) {
                        $creditCurrencyCode = $baseCurrencyCode;
                        $credit->setCurrency($creditCurrencyCode);
                        $credit->setCustomerId($customer->getId());
                    }

                    $voucherCurrency = $this->getModel(\Magento\Directory\Model\Currency::class)
                        ->load($giftVoucher->getCurrency());
                    $creditCurrency = $this->getModel(\Magento\Directory\Model\Currency::class)
                        ->load($creditCurrencyCode);

                    if ($creditCurrencyCode != $giftVoucher->getCurrency()) {
                        $rate   = $this->getHelper()->getRateToCurrentCurrency($voucherCurrency, $creditCurrency);
                        $amount = $balance * $rate;
                    } else {
                        $amount = $balance;
                    }
                    $credit->setBalance($credit->getBalance() + $amount);
                    $nowTime       = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
                    $credithistory = $this->getModel(\Magestore\Giftvoucher\Model\CreditHistory::class)
                        ->setCustomerId($customer->getId())
                        ->setAction('Redeem')
                        ->setCurrencyBalance($credit->getBalance())
                        ->setGiftcardCode($giftVoucher->getGiftCode())
                        ->setBalanceChange($amount)
                        ->setCurrency($credit->getCurrency())
                        ->setCreatedDate($nowTime);
                    $history = $this->getModel(\Magestore\Giftvoucher\Model\History::class)->setData([
                        'order_increment_id' => '',
                        'giftvoucher_id' => $giftVoucher->getId(),
                        'created_at' => $nowTime,
                        'action' => \Magestore\Giftvoucher\Model\Actions::ACTIONS_REDEEM,
                        'amount' => $balance,
                        'balance' => 0.0,
                        'currency' => $giftVoucher->getCurrency(),
                        'status' => $giftVoucher->getStatus(),
                        'order_amount' => '',
                        'comments' => __('Redeem to Gift Card credit balance'),
                        'extra_content' => __('Redeemed by %1', $customer->getName()),
                        'customer_id' => $customer->getId(),
                        'customer_email' => $customer->getEmail(),
                    ]);

                    try {
                        $giftVoucher->setBalance(0)
                            ->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_USED)
                            ->save();
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                        return $this->_redirect("giftvoucher/index/addredeem");
                    }

                    try {
                        $credit->save();
                    } catch (\Exception $e) {
                        $giftVoucher->setBalance($balance)
                            ->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE)
                            ->save();
                        $this->messageManager->addError($e->getMessage());
                        return $this->_redirect("giftvoucher/index/addredeem");
                    }
                    try {
                        $history->save();
                        $credithistory->save();
                        $this->messageManager->addSuccess(__('Gift card "%1" was successfully redeemed', $code));
                        return $this->_redirect("giftvoucher/index/index");
                    } catch (\Exception $e) {
                        $giftVoucher->setBalance($balance)
                            ->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE)
                            ->save();
                        $credit->setBalance($credit->getBalance() - $amount)->save();
                        $this->messageManager->addError($e->getMessage());
                        return $this->_redirect("giftvoucher/index/addredeem");
                    }
                }
            }
        }

        return $this->_redirect("giftvoucher/index/index");
    }
}
