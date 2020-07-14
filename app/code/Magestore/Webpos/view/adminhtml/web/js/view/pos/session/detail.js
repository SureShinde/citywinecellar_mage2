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
        'Magestore_Webpos/js/service/pos/session/balance-service',
        'Magestore_Webpos/js/model/event'
    ],
    function ($, ko, Component, __, PosManagement, CurrencyHelper, BalanceService, Event) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/pos/session/detail'
            },
            error: ko.pureComputed(function () {
                return PosManagement.error();
            }),
            shiftData: ko.computed(function () {
                return PosManagement.shiftData();
            }),
            isClosed: ko.computed(function () {
                return PosManagement.shiftData() && +PosManagement.shiftData().status === PosManagement.CLOSED_STATUS;
            }),
            inClosing: ko.computed(function () {
                return PosManagement.inClosing();
            }),
            theoreticalAmount: ko.observable(BalanceService.getTheoreticalAmount(PosManagement.shiftData())),
            totalCashIn: ko.observable(BalanceService.getTotalCash(PosManagement.shiftData())),
            totalCashOut: ko.observable(BalanceService.getTotalCash(PosManagement.shiftData(), false)),

            realClosingAmount : ko.computed(function(){
                return CurrencyHelper.formatPrice(PosManagement.realClosingAmount());
            }),
            differentAmount : ko.computed(function(){
                return CurrencyHelper.formatPrice(PosManagement.differentAmount());
            }),

            differentReason: ko.computed(function(){
                return PosManagement.differentReason();
            }),

            isShowReason: ko.computed(function(){
                if (PosManagement.differentAmount()){
                    return 1;
                } else {
                    return 0;
                }
            }),

            initialize: function () {
                this._super();
                return this;
            },
            initObservable: function () {
                this._super();
                this.shiftData.subscribe(function (data) {
                    this.theoreticalAmount(BalanceService.getTheoreticalAmount(data));
                    this.totalCashIn(BalanceService.getTotalCash(data));
                    this.totalCashOut(BalanceService.getTotalCash(data, false));
                }.bind(this));
                return this;
            },
            refreshData: function () {
                PosManagement.refreshData();
            },
            formatPrice: function (amount) {
                return CurrencyHelper.formatPrice(amount);
            },
            showCashIn: function () {
                this.containers.forEach(function(parent) {
                    if(typeof parent.showCashTransactions === 'function') {
                        parent.showCashTransactions(true);
                    }
                });
            },
            showCashOut: function () {
                this.containers.forEach(function(parent) {
                    if(typeof parent.showCashTransactions === 'function') {
                        parent.showCashTransactions(false);
                    }
                })
            },
            putMoneyIn : function() {
                Event.dispatch('start_put_money_in', '');
            },
            takeMoneyOut : function() {
                Event.dispatch('start_take_money_out', '');
            },
            closeSession : function(){
                Event.dispatch('start_close_session', '');
            },
            validate : function() {
                Event.dispatch('valiate_session','');
            },
            printReport : function () {
                Event.dispatch('print_x_report', '');
            },

        });
    }
);
