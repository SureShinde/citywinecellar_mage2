/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/components/insert-form',
], function ($, Insert) {
    'use strict';

    /**
     * Get page actions element.
     *
     * @param {String} elem
     * @param {String} actionsClass
     * @returns {String}
     */
    function getPageActions(elem, actionsClass) {
        var el = document.createElement('div');

        el.innerHTML = elem;

        return el.getElementsByClassName(actionsClass)[0];
    }

    /**
     * Return element without page actions toolbar
     *
     * @param {String} elem
     * @param {String} actionsClass
     * @returns {String}
     */
    function removePageActions(elem, actionsClass) {
        var el = document.createElement('div'),
            actions;

        el.innerHTML = elem;
        actions = el.getElementsByClassName(actionsClass)[0];
        if(actions) {
            el.removeChild(actions);
        }
        return el.innerHTML;
    }

    return Insert.extend({

        /** @inheritdoc */
        onRender: function (data) {
            var actions = getPageActions(data, this.pageActionsClass);

            if (!data.length) {
                return this;
            }
            data = removePageActions(data, this.pageActionsClass);
            this.renderActions(actions);
            this.loading(false);
            this.set('content', data);
            this.isRendered = true;
            this.startRender = false;
        },
        
        submit: function(){
            var form = $('#' + this.formSubmitId);
            if (form.validation() && form.validation('isValid')) {
                this.loading(true);
                form.submit();
            }
        }
    });
});
