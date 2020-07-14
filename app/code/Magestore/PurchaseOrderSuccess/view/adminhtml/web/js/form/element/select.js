/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'mageUtils',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'uiLayout'
], function (_, utils, registry, Abstract, layout) {
    'use strict';
    return Abstract.extend({

        /**
         * Callback that fires when 'value' property is updated.
         */
        onUpdate: function () {
            this.bubble('update', this.hasChanged());
            if(this.reloadObjectListing){
                var listing = registry.get(this.reloadObjectListing);
                if(this.reloadParam)
                    listing.externalSource().set('params.'+this.reloadParam, this.value());
            }
            
            this.validate();
        },
    });
});
