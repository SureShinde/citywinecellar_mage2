/*
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

define([
    'uiRegistry',
    'Magento_Ui/js/grid/provider'
], function (registry, Provider) {
    'use strict';

    return Provider.extend({
        processData: function (data) {
            var prefix  = 'inventory_report_stockbylocation.'
                        + 'inventory_report_stockbylocation.'
                        + 'select_locations.general_information.';
            var location = registry.get(prefix + 'location');
            var metric = registry.get(prefix + 'metric');
            if (location && JSON.stringify(location.value()) != JSON.stringify(data.location)) {
                this.location = data.location;
                location.value(data.location);
            }
            if (metric && metric.value() != data.metric) {
                this.metric = data.metric;
                metric.value(data.metric);
            }
            return this._super();
        },
    });
});
