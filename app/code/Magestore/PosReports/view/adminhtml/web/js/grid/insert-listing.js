/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/components/insert-listing',
    'uiRegistry',
    'mageUtils',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function ($, _, InsertListing, registry, utils, alert, $t) {
    'use strict';

    return InsertListing.extend({
        defaults: {
            submit_url: "",
            afterSubmitActions: [],
            submitAdditionalParams: []
        },
        submit: function () {
            var self = this;
            if (self.submit_url) {
                var listing = self.externalListing();
                if (listing) {
                    var columnsIndex = listing.index + '_columns';
                    if (listing.hasChild(columnsIndex)) {
                        var columns = listing.getChild(columnsIndex);
                        if (columns.hasChild('ids')) {
                            var selectionColumn = columns.getChild('ids');
                            var params = self.getSubmitParams(selectionColumn);
                            var request = self.submitData(params, {});
                            request.done(self.afterSubmit.bind(self)).fail(self.submitError.bind(self));
                        }
                    }
                }
            } else {
                self.afterSubmit();
            }
        },
        afterSubmit: function () {
            var self = this;
            if (self.afterSubmitActions) {
                self.afterSubmitActions.each(function (action) {
                    if (action) {
                        self.triggerAction(action);
                    }
                })
            }
            self.loading(false);
        },
        submitError: function (xhr) {
            var self = this;
            self.loading(false);
            if (xhr.statusText === 'abort') {
                return;
            }

            alert({
                content: $t('Something went wrong.')
            });
        },
        triggerAction: function (action) {
            var self = this;
            var targetName = action.targetName,
                params = action.params || [],
                actionName = action.actionName,
                target;

            target = registry.async(targetName);

            if (target && typeof target === 'function' && actionName) {
                params.unshift(actionName);
                params.forEach(function (data, key) {
                    var externalParams = self.getExternalParams(data, true);
                    if (!$.isEmptyObject(externalParams)) {
                        params[key] = externalParams;
                    }
                });
                target.apply(target, params);
            }
        },
        getSubmitParams: function (selectionColumn) {
            var self = this;
            var data = selectionColumn ? selectionColumn.getSelections() : null,
                itemsType,
                result = {};

            if (data) {
                itemsType = data.excludeMode ? 'excluded' : 'selected';
                result.filters = data.params.filters;
                result.search = data.params.search;
                result.namespace = data.params.namespace;
                result[itemsType] = data[itemsType];
                if (self.submitAdditionalParams) {
                    var externalParams = self.getExternalParams(self.submitAdditionalParams);
                    result = _.extend(externalParams, result);
                }

                if (!result[itemsType].length) {
                    result[itemsType] = false;
                }
            }
            return result;
        },

        submitData: function (params, ajaxSettings) {
            var query = utils.copy(params);

            ajaxSettings = _.extend({
                url: this['submit_url'],
                method: 'POST',
                data: query,
                dataType: 'json'
            }, ajaxSettings);

            this.loading(true);

            return $.ajax(ajaxSettings);
        },

        getExternalParams: function (settings, importOnly) {
            var params = {};
            _.each(settings, function (param, key) {
                if (key == "imports") {
                    _.each(param, function (path, name) {
                        var tempInfo = path.split(":");
                        if (tempInfo.length > 1) {
                            var target = registry.get(tempInfo[0]);
                            if (target) {
                                params[name] = target.get(tempInfo[1]);
                            }
                        }
                    });
                } else {
                    if (!importOnly) {
                        params[key] = param;
                    }
                }
            });
            return params;
        }
    });
});
