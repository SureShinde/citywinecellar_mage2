define([
    'Magento_Ui/js/form/element/ui-select'
], function (Select) {
    'use strict';
        return Select.extend({
            initConfig: function (config) {
                if (config.options.length === 0) {
                    config.closeBtn = false;
                }
                this._super();
                return this;
            },
        });
    }
);
