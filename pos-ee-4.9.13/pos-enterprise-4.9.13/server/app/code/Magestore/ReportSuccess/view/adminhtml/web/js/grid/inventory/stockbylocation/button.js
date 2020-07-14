/*
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiRegistry',
    'uiLayout',
    'Magento_Ui/js/form/element/abstract'
], function ($, registry, layout, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            elementTmpl: 'Magestore_ReportSuccess/grid/inventory/stockbylocation/button'
        },
        showReport: function() {
            $('body').trigger('processStart');

            var self = this;
            $.ajax({
                url: self.submit_url,
                data: {
                    'form_key': window.FORM_KEY,
                    location: self.source.location,
                    metric: self.source.metric,
                },
                dataType: 'json',
                success: function (resp) {
                    if (resp.ajaxExpired) {
                        window.location.href = resp.ajaxRedirect;
                    }
                    // Update grid
                    var grid = registry.get(
                        'inventory_report_stockbylocation.' +
                        'inventory_report_stockbylocation.' +
                        'inventory_report_stockbylocation_columns'
                    );
                    layout(resp, grid);
                    grid.elems().forEach(function(el) {
                        undefined === resp[el.index] && el.destroy();
                    });
                    grid.source.reload();
                },
                complete: function () {
                    $('body').trigger('processStop');
                },
            });
        },
    });
});
