/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'mage/translate',
        'Magestore_Webpos/js/model/request',
        'Magestore_Webpos/js/helper/currency-helper',
        'moment',
        'mageUtils',
        'Magestore_Webpos/js/helper/cookie-helper',
        'Magestore_Webpos/js/helper/denomination-helper',
        'Magestore_Webpos/js/service/pos/session/balance-service'
    ],
    function (ko, __, Request, CurrencyHelper ,moment,utils, Cookie, Denomination,BalanceService){
        'use strict';

        var PosManagement = {
            data: null,
            shiftData: ko.observable(),
            error: ko.observable(),
            refreshUrl: ko.observable(),
            saveUrl : ko.observable(),
            saveCloseShiftUrl : ko.observable(),
            CLOSED_STATUS: 1,
            CLOSING_STATUS : 2,
            inClosing : ko.observable(false),
            closingSessionName : ko.observable(),
            realClosingAmount : ko.observable(),
            differentAmount : ko.observable(),
            theoreticalAmount : ko.observable(),
            differentReason: ko.observable(),
            /**
             * Initialize
             * @returns {PosManagement}
             */
            initialize: function () {
                this.initObserver();
                this.resetData();
                this.initData();
                return this;
            },
            /**
             * Init Observer
             * @returns {PosManagement}
             */
            initObserver: function () {

            },
            /**
             * Reset data
             * @returns {PosManagement}
             */
            resetData: function () {

            },
            /**
             * Init Data
             * @param data
             * @param isJson
             */
            initData: function (data) {
                if (data) {
                    if(typeof data === 'string') {
                        data = JSON.parse(data);
                    }
                    this.shiftData(data);
                    this.error(data.error);
                    this.refreshUrl(data.refresh_url);
                    this.saveUrl(data.save_url);
                    this.saveCloseShiftUrl(data.save_close_shift_url);
                    var cname = data.shift_increment_id+'/cname';
                    this.closingSessionName(cname);
                    this.theoreticalAmount(BalanceService.getTheoreticalAmount(data));
                    this.differentReason(data.closed_note);
                    if(Cookie.getCookie(this.closingSessionName())){
                        this.inClosing(true);
                    }
                    var shiftId = data.shift_increment_id;
                    var url = window.location.href;
                    if(Cookie.getCookie(shiftId) && this.inClosing() && url.indexOf('shift/view') === -1){
                        var realDenomation = Cookie.getCookie(shiftId);
                        realDenomation = JSON.parse(realDenomation);
                        var value = Denomination.getTotalRealDenomination(realDenomation);
                        this.setRealClosingAmount(value);
                        this.setDefferentAmount();
                    }else{
                        this.setRealClosingAmount(data.closed_amount);
                        this.setDefferentAmount(data.closed_amount - this.theoreticalAmount());
                    }
                }
            },
            /**
             * Refresh data
             */
            refreshData: function () {
                Request.send(this.refreshUrl(), 'get').then(function(response) {
                    if(response) {
                        if(response.session_data){
                            this.initData(response.session_data);
                        }
                        if(response.currency_data){
                            CurrencyHelper.initData(response.currency_data);
                        }
                    }
                }.bind(this));
            },
            /**
             * save Data
             */
            saveData : function (param, deferred) {
                var self = this;
                var params = this.prepareDataToAdjustCash(param);
                Request.send(this.saveUrl(),'post',params).then(function(response){
                    if(response && response.items){
                        var oldShift = self.shiftData();
                        oldShift.cash_transaction = response.items;
                        deferred.resolve(response);
                    }else{
                        deferred.reject(response);
                    }
                }.bind(this))
                    .catch(function(response){
                       alert('Something went worng! Please check your internet.');
                });
                return deferred;
            },

            saveCloseShift : function (param,deferred ) {
                var self = this;
                var params = this.prepareDataToCloseShift(param);
                Request.send(this.saveCloseShiftUrl(),'post',params)
                .then(function(response){
                        if(response && response.success){
                            self.refreshData();
                            deferred.resolve(response);
                        }else{
                            deferred.reject(response);
                        }
                }.bind(this))
                .catch(function(response){
                    alert('Something went worng! Please check your internet.');
                });
                return deferred;
            },

            /**
             *
             * @param param
             * @returns params
             */
            prepareDataToAdjustCash : function (param){
                let currentTimestamp = new Date().getTime();
                var posName = this.shiftData().pos_name.replace(' ','');
                var transaction_increment_id = posName+'-'+currentTimestamp;
                var todayDate = this.getCurrentDate();
                return {
                    'base_currency_code' : this.shiftData().base_currency_code,
                    'base_value' : param.amount,
                    'created_at' : todayDate,
                    'location_id' : this.shiftData().location_id,
                    'note' : param.reason,
                    'order_increment_id' : '',
                    'shift_increment_id' : this.shiftData().shift_increment_id,
                    'transaction_currency_code' : this.shiftData().transaction_currency_code,
                    'transaction_increment_id' :transaction_increment_id,
                    'type' : param.type,
                    'updated_at' :todayDate,
                    'value': param.amount
                }
            },

            prepareDataToCloseShift : function (param) {
                var todayDate = this.getCurrentDate();
                return {
                    base_closed_amount: param.base_closed_amount,
                    closed_amount: param.closed_amount,
                    base_opening_amount: this.shiftData().base_opening_amount,
                    opening_amount: this.shiftData().opening_amount,
                    closed_note: param.closed_note,
                    location_id: this.shiftData().location_id,
                    opened_at: this.shiftData().opened_at,
                    pos_id: this.shiftData().pos_id,
                    shift_currency_code: this.shiftData().shift_currency_code,
                    base_currency_code: this.shiftData().base_currency_code,
                    shift_increment_id: this.shiftData().shift_increment_id,
                    staff_id: this.shiftData().staff_id,
                    status: param.status,
                    updated_at: todayDate,
                    closed_at: todayDate,
                };
            },
            setInClosing : function (){
                Cookie.setCookie(this.closingSessionName(),true,1);
                this.inClosing(true);
            },
            removeInClosing : function (){
                 this.inClosing(false);
                 Cookie.removeCookie(this.closingSessionName());
            },
            setRealClosingAmount : function (value) {
                this.realClosingAmount(value);
            },
            setDefferentAmount : function (){
                var theoreticalAmount = BalanceService.getTheoreticalAmount(this.shiftData());
                var value = this.realClosingAmount() - theoreticalAmount;
                this.differentAmount(value);
            },
            getCurrentDate : function () {
                var todayDate, momentFormat,
                    inputFormat = 'yy-M-d HH:mm:ss';
                momentFormat = utils.convertToMomentFormat(inputFormat);
                todayDate = moment().format(momentFormat);
                return todayDate;
            },
            getShowCurrentDate : function () {
                return moment().format('llll');
            },
            formatDate : function (date) {
                return moment(date).format('llll');
            }
        };
        return PosManagement.initialize();
    }
);
