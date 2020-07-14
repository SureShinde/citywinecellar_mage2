/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'mage/translate',
        'Magestore_Webpos/js/model/pos/session/management',
        'Magestore_Webpos/js/helper/currency-helper',
        'Magestore_Webpos/js/helper/denomination-helper'
    ],
    function ($, ko, Component, __, PosManagement, CurrencyHelper, DenominationHelper) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/pos/session',
                session_data: null,
                currency_data: null,
                denomination_data: null,
                is_show_cash_transactions: ko.observable(false),
                is_show_cash_in: ko.observable(true)
            },
            initialize: function () {
                this._super();
                console.log(JSON.parse(this.session_data));
                if (this.session_data) {
                    PosManagement.initData(this.session_data);
                }
                //console.log(JSON.parse(this.currency_data));
                if (this.currency_data) {
                    CurrencyHelper.initData(this.currency_data);
                }
                //console.log(JSON.parse(this.denomination_data));
                if (this.denomination_data){
                    DenominationHelper.initData(this.denomination_data);
                }
                return this;
            },
            showCashTransactions: function(isCashIn) {
                this.is_show_cash_transactions(true);
                this.is_show_cash_in(isCashIn);
            },
            hideCashTransactions: function() {
                this.is_show_cash_transactions(false);
            }
        });
    }
);
