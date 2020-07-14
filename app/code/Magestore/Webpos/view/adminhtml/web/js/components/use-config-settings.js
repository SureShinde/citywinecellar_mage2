/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/single-checkbox'
], function (checkbox) {
    'use strict';

    return checkbox.extend({
        defaults: {
            valueFromConfig: '',
            linkedValue: ''
        },

        /**
         * @returns {Element}
         */
        initObservable: function () {

            this._super()
                .observe(['valueFromConfig', 'linkedValue']);
            return this;
        },

        /**
         * @inheritdoc
         */
        'onCheckedChanged': function (newChecked) {
            if (newChecked) {
                console.log('checked');
                console.log(this.valueFromConfig());
                this.linkedValue(this.valueFromConfig());
            }

            this._super(newChecked);
        }
    });
});
