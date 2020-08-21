<?php

namespace Magestore\Webpos\Model\Integration;

use Magento\Framework\Exception\LocalizedException;
use Magestore\Webpos\Api\Integration\GiftcardManagementInterface;
use Magestore\Webpos\Model\Checkout\CouponManagement;

/**
 * Model GiftcardManagement
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftcardManagement extends CouponManagement implements GiftcardManagementInterface
{
    /**
     * Apply
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\QuoteInterface $quote
     * @param string $giftcode
     * @param array $existed_codes
     * @return \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardResponseInterface
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function apply(
        \Magestore\Webpos\Api\Data\Checkout\QuoteInterface $quote,
        $giftcode = null,
        $existed_codes = []
    ) {
        $newQuote = $this->createQuote($quote);
        $newQuote->save();
        $newQuote->collectTotals();
        $error = null;

        if (!$giftcode) {
            $error = __('Please enter or select a gift code');
        }

        $giftcodes = [$giftcode];

        if (!empty($existed_codes)) {
            $giftcodes = array_merge($existed_codes, $giftcodes);
            $giftcodes = array_unique($giftcodes);
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $calculatorService = $objectManager->create(
            \Magestore\Giftvoucher\Service\Redeem\CalculationService::class
        );

        $appliedCodes = [];

        foreach ($newQuote->getAllAddresses() as $address) {
            if ($newQuote->isVirtual() && $address->getAddressType() == 'shipping') {
                continue;
            }
            if (!$newQuote->isVirtual() && $address->getAddressType() == 'billing') {
                continue;
            }
            $giftCodeManagementService = $objectManager
                ->create(\Magestore\Giftvoucher\Api\GiftCode\GiftCodeManagementServiceInterface::class);
            $usableGiftCodes = $giftCodeManagementService->getUsableGiftCodeCollection($giftcodes);
            foreach ($usableGiftCodes as $code) {
                if ($calculatorService->validateGiftCode($code, $newQuote, $address)
                    && $calculatorService->validateCustomer($code, $newQuote->getCustomerId())) {
                    /** @var \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardInterface $appliedCode */
                    $appliedCode = $objectManager->create(
                        \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardInterface::class
                    );
                    $appliedCode->setCode($code->getGiftCode());
                    $appliedCode->setBalance($code->getBalance());
                    $appliedCode->setCurrency($code->getCurrency());
                    $validItemIds = [];
                    foreach ($newQuote->getAllItems() as $item) {
                        //Skipping child items to avoid double calculations
                        if ($item->getParentItemId()) {
                            continue;
                        }
                        if ($item->isDeleted()
                            || $item->getProduct()->getTypeId() == 'giftvoucher'
                            || !$code->getActions()->validate($item)) {
                            continue;
                        }
                        $validItemIds[] = $item->getTmpItemId();
                    }
                    $appliedCode->setValidItemIds($validItemIds);
                    $appliedCodes[] = $appliedCode;
                }
            }
        }

        if (!$error && $giftcode) {
            $giftVoucher = $objectManager->create(\Magestore\Giftvoucher\Model\Giftvoucher::class)
                ->loadByCode($giftcode);
            if ($giftVoucher->getId()) {
                if (!$this->validateCustomer($giftVoucher, $newQuote)) {
                    $error = __('Gift code "%1" limits the number of users', $giftcode);
                } elseif ($giftVoucher->getStatus() == 2 && $giftVoucher->isValidWebsite($newQuote->getStoreId())) {
                    $error = __('You can’t use this gift code "%1" since its conditions haven’t been met.', $giftcode);
                } else {
                    $error = __('Gift Card "%1" is no longer available to use', $giftcode);
                }
            } else {
                $error = __('Gift code "%1" does not exist', $giftcode);
            }
            foreach ($appliedCodes as $appliedCode) {
                if (trim($appliedCode->getCode()) == $giftcode) {
                    $error = null;
                }
            }
        }
        $newAppliedCodes = [];
        foreach ($giftcodes as $code) {
            foreach ($appliedCodes as $appliedCode) {
                if (trim($appliedCode->getCode()) === $code) {
                    $newAppliedCodes[] = $appliedCode;
                }
            }
        }

        $this->deleteQuote($newQuote);

        /** @var \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardResponseInterface $response */
        $response = $objectManager->create(
            \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardResponseInterface::class
        );
        $response->setAppliedCodes($newAppliedCodes);

        $customerId = $newQuote->getCustomerId();
        $customerEmail = $newQuote->getCustomerEmail();
        $giftcodeCollection = $objectManager->create(
            \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\Collection::class
        );
        $giftcodeCollection->addFieldToFilter('main_table.customer_id', $newQuote->getCustomerId());
        $giftcodeCollection->getExistedGiftcodes($customerId, $customerEmail);
        $existingCodes = [];
        foreach ($giftcodeCollection as $code) {
            $existingCode = $objectManager->create(
                \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardInterface::class
            );
            $existingCode->setCode($code->getGiftCode());
            $existingCode->setBalance($code->getBalance());
            $existingCode->setCurrency($code->getCurrency());
            $existingCodes[] = $existingCode;
        }
        $response->setExistingCodes($existingCodes);
        $response->setError($error);
        return $response;
    }

    /**
     * Validate Customer
     *
     * @param string $giftvoucher
     * @param \Magento\Quote\Model\Quote $quote
     * @return bool
     */
    public function validateCustomer($giftvoucher, \Magento\Quote\Model\Quote $quote)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig */
        $scopeConfig = $objectManager->create(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $shareCard = $scopeConfig->getValue(
            'giftvoucher/general/share_card',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
            $quote->getStoreId()
        );
        $shareCard = (int)$shareCard;
        if ($shareCard < 1) {
            return true;
        }
        $customersUsed = $giftvoucher->getCustomerIdsUsed();
        if ($shareCard > count($customersUsed) || in_array($quote->getCustomerId(), $customersUsed)) {
            return true;
        }
        return false;
    }

    /**
     * Get Giftcard By Customer
     *
     * @param int $customer_id
     * @return \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardResponseInterface
     * @throws LocalizedException
     */
    public function getGiftcardByCustomer($customer_id = null)
    {
        if (!$customer_id) {
            throw new LocalizedException(__('Please select a customer first.'));
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Customer\Model\CustomerRegistry $customerRegistry */
        $customerRegistry = $objectManager->create(\Magento\Customer\Model\CustomerRegistry::class);
        try {
            $customer = $customerRegistry->retrieve($customer_id);
            $customerEmail = $customer->getEmail();

            /** @var \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardResponseInterface $response */
            $response = $objectManager->create(
                \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardResponseInterface::class
            );

            $giftcodeCollection = $objectManager->create(
                \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\Collection::class
            );
            $giftcodeCollection->addFieldToFilter('main_table.customer_id', $customer_id);
            $giftcodeCollection->getExistedGiftcodes($customer_id, $customerEmail);
            $existingCodes = [];
            foreach ($giftcodeCollection as $code) {
                $existingCode = $objectManager->create(
                    \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardInterface::class
                );
                $existingCode->setCode($code->getGiftCode());
                $existingCode->setBalance($code->getBalance());
                $existingCode->setCurrency($code->getCurrency());
                $existingCodes[] = $existingCode;
            }

            $response->setExistingCodes($existingCodes);
            return $response;
        } catch (\Exception $e) {
            throw new LocalizedException(__('Customer does not exist'));
        }
    }
}
