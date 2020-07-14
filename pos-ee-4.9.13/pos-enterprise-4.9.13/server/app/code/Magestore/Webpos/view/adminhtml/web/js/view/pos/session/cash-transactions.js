/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'uiComponent',
        'mage/translate',
        'Magestore_Webpos/js/model/pos/session/management',
        'Magestore_Webpos/js/helper/currency-helper',
        'Magestore_Webpos/js/service/pos/session/balance-service',
        'moment'
    ],
    function (ko, Component, __, PosManagement, CurrencyHelper, BalanceService, moment) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/pos/session/cash-transactions'
            },
            popup_id: 'cash-transaction-popup',
            is_show_cash_transactions: ko.observable(false),
            is_show_cash_in: ko.observable(true),
            shiftData: ko.computed(function () {
                return PosManagement.shiftData();
            }),
            totalCashIn: ko.observable(BalanceService.getTotalCash(PosManagement.shiftData())),
            totalCashOut: ko.observable(BalanceService.getTotalCash(PosManagement.shiftData(), false)),
            initialize: function () {
                this._super();
                return this;
            },
            initContainer: function (parent) {
                var self = this._super(parent);
                if(parent && typeof parent.showCashTransactions === 'function') {
                    parent.is_show_cash_transactions.subscribe(function (data) {
                        document.body.appendChild($('cash-transaction-popover'));
                        self.is_show_cash_transactions(data);
                    });
                    parent.is_show_cash_in.subscribe(function (data) {
                        self.is_show_cash_in(data);
                    });
                }
                return this;
            },
            formatPrice: function (amount) {
                return CurrencyHelper.formatPrice(amount);
            },
            formatDate : function (date){
                return PosManagement.formatDate(date);
            },
            showCashIn: function(isCashIn) {
                this.containers.forEach(function(parent) {
                    if(typeof parent.showCashTransactions === 'function') {
                        parent.showCashTransactions(isCashIn);
                    }
                })
            },
            hideCashTransaction: function () {
                this.containers.forEach(function(parent) {
                    if(typeof parent.hideCashTransactions === 'function') {
                        parent.hideCashTransactions();
                    }
                })
            }
        });
    }
);
