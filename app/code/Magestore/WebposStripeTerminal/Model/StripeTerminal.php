<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripeTerminal\Model;

/**
 * Class StripeTerminal
 * @package Magestore\WebposStripeTerminal\Model
 */
class StripeTerminal extends \Magestore\WebposStripe\Model\Stripe
{
    const PAYMENT_METHOD = 'stripeterminal';

    /**
     * @var \Magestore\WebposStripeTerminal\Helper\Data
     */
    protected $helper;

    /**
     * StripeTerminal constructor.
     * @param \Magestore\WebposStripeTerminal\Helper\Data $helper
     */
    public function __construct(
        \Magestore\WebposStripeTerminal\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }
}
