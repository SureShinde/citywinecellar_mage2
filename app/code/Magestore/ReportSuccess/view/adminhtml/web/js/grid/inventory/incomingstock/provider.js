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
            {id: 'incoming_stock', key: 'incoming_stock'},
            {id: 'overdue_incoming_stock', key: 'overdue_incoming_stock'},
            {id: 'total_cost', key: 'total_cost'}
        ],
        listPrice: ['total_cost']
    });
});