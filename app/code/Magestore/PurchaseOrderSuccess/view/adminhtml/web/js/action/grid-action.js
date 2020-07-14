
/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global $, $H */

define([
    'jquery',
    'mage/adminhtml/grid',
], function (jQuery) {
    return function(url, params, gridObject, callback){
        if(typeof gridObject == 'string')
            gridObject = window[gridObject];
        if(!url)
            url = gridObject.url;
        var filters = $$('#' + gridObject.containerId + ' [data-role="filters-form"] input', '#' +
            gridObject.containerId + ' [data-role="filters-form"] select');
        var elements = [];
        for (var i in filters) {
            if (filters[i].value && filters[i].value.length) elements.push(filters[i]);
        }
        var url = gridObject._addVarToUrl(url, gridObject.filterVar, Base64.encode(Form.serializeElements(elements)));

        gridObject.reloadParams = gridObject.reloadParams || {};
        gridObject.reloadParams.form_key = FORM_KEY;
        if(params){
            jQuery.each(params, function(index, value){
                gridObject.reloadParams[index] = value;
            })
        }
        var ajaxSettings = {
            url: url + (url.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true' ),
            showLoader: true,
            method: 'post',
            context: jQuery('#' + gridObject.containerId),
            data: gridObject.reloadParams,
            error: gridObject._processFailure.bind(gridObject),
            complete: gridObject.initGridAjax.bind(gridObject),
            dataType: 'html',
            success: function(data, textStatus, transport) {
                gridObject._onAjaxSeccess(data, textStatus, transport);
                if(callback)
                    callback();
            }.bind(gridObject)
        };
        jQuery('#' + gridObject.containerId).trigger('gridajaxsettings', ajaxSettings);
        var ajaxRequest = jQuery.ajax(ajaxSettings);
        jQuery('#' + gridObject.containerId).trigger('gridajax', ajaxRequest);
        return ajaxRequest;
    }
})