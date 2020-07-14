/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mageUtils',
    'uiClass',
    'Magento_Ui/js/form/client',
    'uiRegistry'
], function ($, _, utils, Class, Client, registry) {
    'use strict';

    /**
     * Before save validate request.
     *
     * @param {Object} data
     * @param {String} url
     * @param {String} selectorPrefix
     * @param {String} messagesClass
     * @returns {*}
     */
    function beforeSave(data, url, selectorPrefix, messagesClass) {
        var save = $.Deferred();

        data = utils.serialize(data);

        data['form_key'] = window.FORM_KEY;

        if (!url || url === 'undefined') {
            return save.resolve();
        }

        $('body').trigger('processStart');

        $.ajax({
            url: url,
            data: data,

            /**
             * Success callback.
             * @param {Object} resp
             * @returns {Boolean}
             */
            success: function (resp) {
                if (!resp.error) {
                    save.resolve();

                    return true;
                }

                $('body').notification('clear');
                $.each(resp.messages || [resp.message] || [], function (key, message) {
                    $('body').notification('add', {
                        error: resp.error,
                        message: message,

                        /**
                         * Insert method.
                         *
                         * @param {String} msg
                         */
                        insertMethod: function (msg) {
                            var $wrapper = $('<div/>').addClass(messagesClass).html(msg);

                            $('.page-main-actions', selectorPrefix).after($wrapper);
                        }
                    });
                });
            },

            /**
             * Complete callback.
             */
            complete: function () {
                $('body').trigger('processStop');
            }
        });

        return save.promise();
    }

    return Client.extend({

        /**
         * Assembles data and submits it using 'utils.submit' method
         */
        save: function (data, options, reload_data) {
            var url = this.urls.beforeSave,
                save = this._save.bind(this, data, options);

            beforeSave(data, url, this.selectorPrefix, this.messagesClass).then(save);
            if (reload_data) {
                console.log(reload_data);
                //os_supplier_product_listingJsObject.resetFilter();
            }
            //console.log(options);
            return this;
        },

        /**
         * Apply action on target component,
         * but previously create this component from template if it is not existed
         *
         * @param {Object} action - action configuration
         */
        reloadAction: function (targetName, actionName) {
            var params = [],
                target;
            if (!actionName) {
                actionName = 'reload';
            }
            if (!registry.has(targetName)) {
                this.getFromTemplate(targetName);
            }
            target = registry.async(targetName);
            if (target && typeof target === 'function' && actionName) {
                params.unshift(actionName);
                target.apply(target, params);
            }
        },

        /**
         * Handles ajax success callback.
         *
         * @param {jQueryPromise} promise - Promise to be resoloved.
         * @param {*} data - See 'jquery' ajax success callback.
         */
        onSuccess: function (promise, data) {
            console.log('ssss');
            var errors;

            if (data.error) {
                errors = _.map(data.messages, this.createError, this);

                promise.reject(errors);
            } else {
                promise.resolve(data);
            }
        }
    });
});
