/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/provider',
    './client',
], function (Provider, Client) {
    'use strict';

    return Provider.extend({
        //defaults: {
        //    clientConfig: {
        //        urls: {
        //            save: '${ $.submit_url }',
        //            beforeSave: '${ $.validate_url }',
        //            reload_data: '${ $.reload_data }'
        //        }
        //    }
        //},
        /**
         * Initializes client component.
         *
         * @returns {Provider} Chainable.
         */
        initClient: function () {
            this.client = new Client(this.clientConfig);

            return this;
        },

        /**
         * Saves currently available data.
         *
         * @param {Object} [options] - Addtitional request options.
         * @returns {Provider} Chainable.
         */
        save: function (options) {
            var data = this.get('data');
            var reload_data = this.reload_data;
            this.client.save(data, options, reload_data);

            return this;
        },
    });
});