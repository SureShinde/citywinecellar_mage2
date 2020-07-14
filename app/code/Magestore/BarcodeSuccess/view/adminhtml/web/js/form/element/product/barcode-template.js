/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'mageUtils',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, utils, registry, Abstract) {
    'use strict';
    return Abstract.extend({

        /**
         * Callback that fires when 'value' property is updated.
         */
        onUpdate: function () {
            this.bubble('update', this.hasChanged());
            if(this.previewButton){
                registry.get(this.previewButton).previewTemplate('preview');
            }
            this.validate();
        },
    });
});
