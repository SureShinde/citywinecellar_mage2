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
    'Magestore_PurchaseOrderSuccess/js/action/grid-action'
], function ($, Insert, utils, registry, layout, alert, _, gridAction) {
    'use strict';

    return Insert.extend({
        defaults: {
            modules: {
                magestore_dataProvider: '${ $.externalProvider }'
            }
        },

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

            // Abel edit: suggest quantity
            var msDataprovider = this.magestore_dataProvider();
            // console.log(msDataprovider);
            var suggest_qty = [];
            // suggest for back order
            if(msDataprovider.dataScope === "os_purchase_order_back_order_product") {
                var productList = msDataprovider.data.items;
                selections.selected._each(function (prdId) {
                    productList.each(function (item) {
                        if(item.entity_id === prdId) {
                            suggest_qty.push({qty: Math.abs(item.qty), id: prdId});
                            return;
                        }
                    });
                });
            }
            // console.log(suggest_qty);
            var params = {isAjax: 'true', session_id: this.session_id, selected: selections.selected, suggest_qty: suggest_qty};
            this.loading(true);
            
            $.ajax({
                method: "POST",
                url: this.save_url,
                data: params
            }).done(function(transport) {
                result.resolve(transport);
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
                function (transport) {
                    this.loading(false);
                    if(this.closeModal)
                        this.doAction(this.closeModal, 'closeModal');
                    if(this.reloadObjects)
                        this.reloadObjects.forEach(function(reloadObject){
                            self.processReload(reloadObject);
                        });
                    if (!this.realTimeLink) {
                        this.updateValue();
                    }
                    // reload total block
                    jQuery('#purchase_sumary_total_block').replaceWith(transport);
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
                this.doAction(reloadObject.name);
        },

        /**
         * Apply action on target component,
         * but previously create this component from template if it is not existed
         *
         * @param {Object} action - action configuration
         */
        doAction: function (targetName, actionName) {
            var params = [],
                target;
            if(!actionName)
                actionName = 'reload';

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
