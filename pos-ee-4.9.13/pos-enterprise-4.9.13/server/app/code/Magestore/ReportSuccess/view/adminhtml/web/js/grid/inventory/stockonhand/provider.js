/*
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magestore_ReportSuccess/js/grid/inventory/provider'
], function ($, ko, Provider) {
    'use strict';

    return Provider.extend({
        listTotal: [
            {id: 'qty_on_hand', key: 'qty_on_hand'},
            {id: 'stock_value', key: 'stock_value'},
            {id: 'potential_revenue', key: 'potential_revenue'},
            {id: 'potential_profit', key: 'potential_profit'}
        ],
        listPrice: ['stock_value','potential_revenue','potential_profit']
    });
});