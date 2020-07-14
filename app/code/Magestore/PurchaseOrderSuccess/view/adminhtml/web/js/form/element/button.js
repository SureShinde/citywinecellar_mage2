/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/components/button',
    'uiRegistry',
    'uiLayout',
    'mageUtils',
    'underscore'
], function (Element, registry, layout, utils, _) {
    'use strict';

    return Element.extend({
        defaults: {
            additionalClasses: {},
            displayArea: 'outsideGroup',
            displayAsLink: false,
            elementTmpl: 'Magestore_PurchaseOrderSuccess/form/element/button',
            template: 'ui/form/components/button/simple',
            visible: true,
            disabled: false,
            title: ''
        },

        /**
         * Performs configured actions
         */
        action: function () {
            if(this.redirectUrl){
                location.href = this.redirectUrl;
                return;
            }
            this.actions.forEach(this.applyAction, this);
        },

        /**
         * Apply action on target component,
         * but previously create this component from template if it is not existed
         *
         * @param {Object} action - action configuration
         */
        applyAction: function (action) {
            var targetName = action.targetName,
                params = action.params || [],
                extendParams = [],
                actionName = action.actionName,
                target;

            if(action.fields){
                target = registry.get(targetName);
                params = target.externalSource().params;
                action.fields.forEach(function(el){
                    if (!registry.has(el)) {
                        this.getFromTemplate(el);
                    }
                    var field = registry.get(el);
                    params[field.index] = field.value();
                });
                if (!registry.has(targetName)) {
                    this.getFromTemplate(targetName);
                }
                target.externalSource().set('params', params);
                target.reload();
                return;
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
    });
});
