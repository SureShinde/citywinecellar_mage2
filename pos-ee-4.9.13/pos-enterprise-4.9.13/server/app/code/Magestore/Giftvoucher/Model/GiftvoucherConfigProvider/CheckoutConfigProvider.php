<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\Giftvoucher\Model\GiftvoucherConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class \Magestore\Giftvoucher\Model\GiftvoucherConfigProvider\CheckoutConfigProvider
 */
class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * Settings Path
     */
    const XML_PATH_ENABLE_GIFT_CARD_BOX_CHECKOUT = 'giftvoucher/interface_payment/show_gift_card';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get gift card config
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'GiftCardConfig' => [
                'isEnableGiftCardFormCheckout' => (int) $this->scopeConfig->getValue(
                    self::XML_PATH_ENABLE_GIFT_CARD_BOX_CHECKOUT
                )
            ],
        ];
    }
}
