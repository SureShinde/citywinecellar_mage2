/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/components/insert-listing',
    'mageUtils',
    'uiRegistry',
    'uiLayout',
    'Magento_Ui/js/modal/alert',
    'underscore',
    'Magestore_SupplierSuccess/js/action/grid-action'
], function ($, Insert, utils, registry, layout, alert, _, gridAction) {
    'use strict';

    return Insert.extend({
        // /**
        //  * Updates externalValue, from selectionsProvider data (if it is enough)
        //  * or ajax request to server
        //  *
        //  * @returns {Object} result - deferred that will be resolved when value is updated
        //  */
        updateExternalValue: function () {
            var result = $.Deferred(),
                provider = this.selections(),
                selections,
                totalSelected,
                itemsType,
                rows;
        
            if (!provider) {
                return result;
            }
        
            selections = provider && provider.getSelections();
            totalSelected = provider.totalSelected();
            if(!totalSelected){
                return alert({
                    title: $.mage.__('Error'),
                    content: $.mage.__('Please select at least one item.')
                });
            }
            itemsType = selections && selections.excludeMode ? 'excluded' : 'selected';
            var params = this.externalSource().params;
            params.isAjax = 'true';
            params.session_id = this.session_id;
            if(itemsType == 'excluded'){
                params.excluded = selections[itemsType];
                if(selections[itemsType].length==0)
                    params.excluded = 'false';
            }
            if(itemsType == 'selected')
                params.selected = selections[itemsType];
            this.loading(true);
            $.ajax({
                method: "POST",
                url: this.save_url,
                data: params
            }).done(function(transport) {
                result.resolve();
            }).fail(function(transport){
                result.resolve();
            });
        
            return result;
        },
        
        /**
         * Reload source
         */
        reload: function () {
            if(this.isRendered)
                this.externalSource().set('params.t', new Date().getTime());
        },

        /**
         * Updates external value, then updates value from external value
         *
         */
        save: function () {
            var self = this;
            this.updateExternalValue().done(
                function () {
                    this.loading(false);
                    if(this.reloadObjects){
                        this.reloadObjects.forEach(function(reloadObject){
                            self.processReload(reloadObject);
                        });
                    }
                    if (this.closeModal) {
                        this.reloadAction(this.closeModal, 'closeModal');
                    }
                    if (!this.realTimeLink) {
                        this.updateValue();
                    }
                }.bind(this)
            );
        },

        /**
         * Prepare process reload for reload objects
         *
         * @param object
         */
        processReload: function(reloadObject){
            if(reloadObject.type=='block')
                gridAction('','', reloadObject.name);
            if(reloadObject.type=='ui')
                this.reloadAction(reloadObject.name);
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
