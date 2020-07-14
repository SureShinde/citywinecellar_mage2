/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/components/insert-listing',
    'underscore'
], function ($, Insert, _) {
    'use strict';

    return Insert.extend({
        /**
         * Updates externalValue, from ajax request to grab selected rows data
         *
         * @param {Object} selections
         * @param {String} itemsType
         *
         * @returns {Object} request - deferred that will be resolved when ajax is done
         */
        updateFromServerData: function (selections, itemsType) {
            var filterType = selections && selections.excludeMode ? 'nin' : 'in',
                selectionsData = {},
                request;

            _.extend(selectionsData, this.params || {}, selections.params);
            if(this.externalSource && this.externalSource())
                _.extend(selectionsData, selectionsData, this.externalSource().params);
            
            if (selections[itemsType] && selections[itemsType].length) {
                selectionsData.filters = {};
                selectionsData['filters_modifier'] = {};
                selectionsData['filters_modifier'][this.indexField] = {
                    'condition_type': filterType,
                    value: selections[itemsType]
                };
            }

            selectionsData.paging = {
                notLimits: 1
            };

            request = this.requestData(selectionsData, {
                method: this.requestConfig.method
            });
            request
                .done(function (data) {
                    this.setExternalValue(data.items || data);
                    this.loading(false);
                }.bind(this))
                .fail(this.onError);

            return request;
        },
        
        /**
         * Request for render content.
         *
         * @returns {Object}
         */
        render: function (params) {
            var self = this,
                request;

            if (this.isRendered) {
                return this;
            }

            self.previousParams = params || {};

            $.async({
                component: this.name,
                ctx: '.' + this.contentSelector
            }, function (el) {
                self.contentEl = $(el);
                self.startRender = true;
                params = _.extend({}, self.params, params || {});
                if(this.externalSource && this.externalSource())
                    _.extend(params, params, this.externalSource().params);
                request = self.requestData(params, self.renderSettings);
                request
                    .done(self.onRender)
                    .fail(self.onError);
            });

            return this;
        },

        save: function () {
            var result = $.Deferred();
            this.updateExternalValue().done(
                function () {
                    if (!this.realTimeLink) {
                        this.updateValue();
                    }
                    result.resolve();
                }.bind(this)
            );
            return result;
        }
    });
});
