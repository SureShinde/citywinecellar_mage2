/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/element/select',
    'ko'
], function (Select, ko) {
    'use strict';

    function indexOptions(data, result) {
        var value;

        result = result || {};

        data.forEach(function (item) {
            value = item.value;

            if (Array.isArray(value)) {
                indexOptions(value, result);
            } else {
                result[value] = item;
            }
        });

        return result;
    }

    return Select.extend({
        setOptions: function (data) {
            var isVisible;

            this.indexedOptions = indexOptions(data);
            this.options(data);

            if (this.customEntry) {
                isVisible = !!data.length;

                this.setVisible(isVisible);
                this.toggleInput(!isVisible);
            }

            return this;
        },
    });
});
