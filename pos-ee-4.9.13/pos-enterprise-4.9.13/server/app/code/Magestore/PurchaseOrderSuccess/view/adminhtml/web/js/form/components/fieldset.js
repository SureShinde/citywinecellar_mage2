/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/components/fieldset',
    'underscore',
    'uiRegistry',
    'uiLayout',
    'mageUtils'
], function (Fieldset, _, registry, layout, utils) {
    'use strict';

    return Fieldset.extend({
        /**
         * Sets 'opened' flag to true.
         *
         * @returns {Collapsible} Chainable.
         */
        open: function () {
            if (this.collapsible) {
                this.opened(true);
            }
            if(this.actions)
                this.actions.forEach(this.applyAction, this);
            return this;
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
                actionName = action.actionName,
                target;

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
         * Create target component from template
         *
         * @param {Object} targetName - name of component,
         * that supposed to be a template and need to be initialized
         */
        getFromTemplate: function (targetName) {
            var parentName = targetName.split('.'),
                index = parentName.pop(),
                child;

            parentName = parentName.join('.');
            child = utils.template({
                parent: parentName,
                name: index,
                nodeTemplate: targetName
            });
            layout([child]);
        },
    });
});
