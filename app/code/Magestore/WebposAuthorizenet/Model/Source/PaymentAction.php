<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magestore\WebposAuthorizenet\Model\Source;

/**
 * Class PaymentAction
 *
 * Used for payment action source
 */
class PaymentAction implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => \Magento\Payment\Model\MethodInterface::ACTION_AUTHORIZE,
                'label' => __('Authorize Only'),
            ],
            [
                'value' => \Magento\Payment\Model\MethodInterface::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorize and Capture')
            ]
        ];
    }
}
