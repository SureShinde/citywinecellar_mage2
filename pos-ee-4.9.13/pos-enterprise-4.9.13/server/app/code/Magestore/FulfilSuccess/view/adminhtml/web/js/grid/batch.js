/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'jquery',
    'underscore',
    'mage/translate',
    'mageUtils',
    'uiElement',
    'Magento_Ui/js/modal/confirm'
], function (ko, $, _, __, utils, Element, ModalConfirm) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Magestore_FulfilSuccess/grid/batch',
            options: {
            },
        },

        /**
         * Initializes batchs component.
         *
         */
        initialize: function () {
            this._super()
                .updateArray();

            return this;
        },

        /**
         * get options array
         *
         */
        updateArray: function () {
            var array = _.values(this.options);

            this.optionsArray = _.sortBy(array, 'value');
            return this;
        },

        /**
         * get title of remove button
         *
         */
        getRemoveTitle: function () {
            return __('Remove Batch');
        },

        /**
         * filter by batch
         *
         */
        filterBatch: function (batchId) {
            $('.admin__control-select').each(function(index){
                if(this.getAttribute('name') == 'batch_id') {
                    this.value = batchId;
                    var event = new Event('change');
                    this.dispatchEvent(event);
                }
            });

            $('.action-secondary').each(function(index){
                if(this.getAttribute('data-action') == 'grid-filter-apply') {
                    this.click();
                }
            });
        },

        /**
         * remove batch
         *
         */
        removeBatch: function (batchId, removeUrl) {
            var message = __('Are you sure to remove the batch?');
            var self = this;
            ModalConfirm({
                content: message,
                actions: {
                    confirm: function () {
                        $('.admin__control-select').each(function(index){
                            if(this.getAttribute('name') == 'batch_id') {
                                this.value = '';
                                var event = new Event('change');
                                this.dispatchEvent(event);
                            }
                        });
                        $('.action-secondary').each(function(index){
                            if(this.getAttribute('data-action') == 'grid-filter-apply') {
                                this.click();
                            }
                        });
                        var url = removeUrl;
                        window.location.href = url;
                    },
                    cancel: function () {
                        return false;
                    },
                    always: function () {
                        return false;
                    }
                }
            });
        },
    });
});
