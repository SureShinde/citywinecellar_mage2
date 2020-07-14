/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/model/pos/session/management',
        'Magento_Ui/js/modal/modal',
        'mage/translate',
        'Magestore_Webpos/js/model/event',
        'Magestore_Webpos/js/helper/currency-helper'
    ],
    function ($, ko, Component, PosManagement, Modal, __, Event, CurrencyHelper) {
        "use strict";
        return Component.extend({
            type: ko.observable(''),
            amount: ko.observable(0),
            reason: ko.observable(''),
            is_show_cash_adjustment: ko.observable(false),
            adjustmentTitle: ko.observable(),
            adjustmentNotice: ko.observable(),
            add_cash_class: ko.observable('cash_adjustment_active'),
            remove_cash_class: ko.observable('cash_adjustment_inactive'),
            valueErrorMessage: ko.observable(''),
            buttonLable: ko.observable(''),
            defaults: {
                template: 'Magestore_Webpos/pos/session/cash-adjustment',
            },
            initialize: function () {
                this._super();
                var self = this;

                this.valueFormatted = ko.pureComputed(function () {
                    return CurrencyHelper.formatPrice(self.amount());
                }, this);

                this.ishowButton = ko.pureComputed(function () {
                    if (self.amount() > 0 && self.reason()) {
                        return true;
                    }
                    return false;
                })

                /* put money in */
                Event.observer('start_put_money_in', function () {
                    self.clearInitData();
                    self.type('add');
                    self.adjustmentTitle('Put Money In');
                    self.adjustmentNotice('Fill in this form if you put money in the cash-drawer');
                    self.buttonLable('Put In');
                    self.openForm();
                });
                /*taka money out */
                Event.observer('start_take_money_out', function () {
                    self.clearInitData();
                    self.type('remove');
                    self.adjustmentTitle('Take Money Out');
                    self.adjustmentNotice('Fill in this form if you take money from the cash-drawer');
                    self.buttonLable('Take Out');
                    self.openForm();
                });

            },

            openForm: function () {
                this.is_show_cash_adjustment(true);
            },
            cancelPopup: function () {
                this.is_show_cash_adjustment(false);
            },
            valueChange: function (data, event) {
                this.amount(CurrencyHelper.toNumber(event.target.value));
            },
            typetext: function (data, event) {
                this.reason(event.target.value);
            },
            clearInitData: function () {
                this.amount(0);
                this.reason('');
                this.type('');
                this.adjustmentTitle('');
                this.adjustmentNotice('');
                this.buttonLable('');
            },
            submitData: function () {
                var self = this;
                var params = {
                    'reason': this.reason(),
                    'type': this.type(),
                    'amount': this.amount()
                }
                var deferred = $.Deferred();
                PosManagement.saveData(params, deferred);
                deferred.done(function (response) {
                    if (response && response.items) {
                        self.clearInitData();
                        self.cancelPopup();
                    }
                })
            },
        });
    }
);
