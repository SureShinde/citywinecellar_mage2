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
        'Magestore_Webpos/js/smooth-scrollbar',
        'Magestore_Webpos/js/model/event',
        'Magestore_Webpos/js/model/pos/session/management',
        'Magestore_Webpos/js/helper/denomination-helper',
        'Magestore_Webpos/js/helper/currency-helper',
        'Magestore_Webpos/js/helper/cookie-helper',
        'Magestore_Webpos/js/service/pos/session/balance-service',
    ],
    function ($,
              ko,
              Component,
              __,
              scrollbar,
              Event,
              PosManagement,
              DenominationHelper,
              CurrencyHelper,
              Cookie,
              BlanceService
    ) {
        "use strict";
        return Component.extend({
            is_show_closing_session : ko.observable(false),
            staff_name : ko.observable(''),
            pos_name : ko.observable(''),
            denomination : ko.observableArray(DenominationHelper.denomination()),
            isValidate : ko.observable(false),
            reason : ko.observable(''),
            manualClosingBalance: ko.observable(''),
            defaults: {
                template: 'Magestore_Webpos/pos/session/closing-session'
            },
            initialize: function () {
                this._super();
                /* close session */
                var self = this;
                Event.observer('start_close_session', function(){
                    self.openForm();
                });
                Event.observer('valiate_session', function(){
                    var theoreticalClosingBalance = BlanceService.getTheoreticalAmount(PosManagement.shiftData());
                    theoreticalClosingBalance = CurrencyHelper.toNumber(theoreticalClosingBalance);
                    var RealClosingBlance = self.closingBlance();
                    if(theoreticalClosingBalance != RealClosingBlance){
                        self.openValidateForm();
                    }else{
                        self.forceConfirm();
                    }
                });

                self.closingBlance = ko.computed(function() {
                    if (self.manualClosingBalance()){
                        return self.manualClosingBalance();
                    } else {
                        return DenominationHelper.getTotalRealDenomination(self.denomination());
                    }
                });

                self.closingBlanceFormat = ko.pureComputed(function(){
                    return  CurrencyHelper.formatPrice(self.closingBlance());
                });

                self.theoreticalAmount = ko.computed(function(){
                    return CurrencyHelper.formatPrice(PosManagement.theoreticalAmount());
                });
                self.realClosingAmount = ko.computed(function(){
                    return CurrencyHelper.formatPrice(PosManagement.realClosingAmount());
                });
                self.differentAmount = ko.computed(function(){
                    return CurrencyHelper.formatPrice(PosManagement.differentAmount());
                });
                self.ishowButton = ko.pureComputed(function () {
                    if(self.reason()){
                        return true;
                    }
                    return false;
                });

                this.valueFormatted = ko.pureComputed(function () {
                    return CurrencyHelper.formatPrice(self.manualClosingBalance());
                }, this);

            },

            valueChange: function (data, event) {
                this.manualClosingBalance(CurrencyHelper.toNumber(event.target.value));
            },

            initData : function () {
                this.pos_name(PosManagement.shiftData().pos_name);
                this.staff_name(PosManagement.shiftData().staff_name);

                /* if in Closing Shift to edit */
                if(PosManagement.inClosing()){
                    var shiftId = PosManagement.shiftData().shift_increment_id;
                    var cookie = Cookie.getCookie(shiftId);
                    var data = JSON.parse(cookie);
                    this.denomination(data);
                }
            },
            typetext : function (data, event){
                this.reason(event.target.value);
            },
            openForm : function () {
                this.is_show_closing_session(true);
                this.isValidate(false);
                this.initData();
                scrollbar.initAll();
            },
            cancelPopup : function () {
                this.is_show_closing_session(false);
                this.isValidate(false);
            },
            /**
             *
             * @param data
             */
            minusQty : function (data) {
                this.processQty(data,false);
            },
            /**
             *
             * @param data
             */
            plusQty : function(data){
                this.processQty(data,true);
            },
            /**
             *
             * @param data
             * @param action
             */
            processQty : function (data , action){
                var id = data.denomination_id;
                var oldDenomination = this.denomination();
                let Denomination = DenominationHelper.processDenominationData(id, action, oldDenomination);
                this.denomination([]);
                this.denomination(Denomination);
            },
            /**
             *
             * @param amount
             * @returns {*}
             */
            formatPrice : function (amount){
                return CurrencyHelper.formatPrice(amount);
            },
            /**
             *
             */
            confirm : function () {
                var shiftId = this.getShiftId();
                Cookie.setCookie(shiftId,JSON.stringify(this.denomination()),1);
                /* set closing status */
                PosManagement.setInClosing();
                var RealClosingBlance = this.closingBlance();
                PosManagement.setRealClosingAmount(RealClosingBlance);
                PosManagement.setDefferentAmount();
                return this.cancelPopup();
            },

            forceConfirm : function () {
                var self = this;
                var params = {
                    'base_closed_amount' : this.closingBlance(),
                    'closed_amount' : this.closingBlance(),
                    'closed_note': this.reason(),
                    'status': PosManagement.CLOSED_STATUS,
                }
                var deferred = $.Deferred();
                var shiftId = this.getShiftId();
                PosManagement.saveCloseShift(params,deferred);
                deferred.done(function (response) {
                    if(response && response.success){
                        /* remove closing status */
                        PosManagement.removeInClosing();
                        /* remove shif denomination data */
                        Cookie.removeCookie(shiftId);
                        return self.cancelPopup();
                    }
                });
            },

            openValidateForm : function () {
                this.is_show_closing_session(true);
                this.isValidate(true);
                scrollbar.initAll();
            },
            getShiftId(){
                var shiftId = PosManagement.shiftData().shift_increment_id;
                return shiftId;
            },

        });
    }
);
