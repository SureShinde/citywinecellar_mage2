<?php

namespace Magestore\Webpos\Api\Checkout;

interface CouponManagementInterface {
    /**
     * @param \Magestore\Webpos\Api\Data\Checkout\QuoteInterface $quote
     * @param string $coupon_code
     * @return \Magestore\Webpos\Api\Data\Checkout\RuleInterface[]
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function checkCoupon(\Magestore\Webpos\Api\Data\Checkout\QuoteInterface $quote, $coupon_code = null);
}