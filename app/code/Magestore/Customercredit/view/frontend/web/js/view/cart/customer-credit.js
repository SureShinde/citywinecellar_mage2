/*
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
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/*jshint browser:true*/
/*global alert*/
define([
    'jquery',
    'mage/url'
], function ($, urlBuilder) {
    'use strict';

    $.widget('magestore.cartCustomerCredit', {

        _create: function () {
            var self = this;

            $('#checkout-cart-remove-credit-amount-button').on('click', function() {
                self.removeCreditAmount();
            });
        },
        removeCreditAmount: function () {
            var url = urlBuilder.build('customercredit/checkout/amountPost');
            var params = {
                customer_credit: 0
            };

            return $.post(
                url,
                params
            ).done(
                function () {
                    window.location.reload();
                }
            )
        }
    });

    return $.magestore.cartCustomerCredit;
});
