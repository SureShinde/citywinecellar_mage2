/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magestore_OrderSuccess/js/grid/columns/detail',
    'jquery',
], function (Column, $) {
    'use strict';

    return Column.extend({
        defaults: {
            modalId: 'verify_order_detail_holder',
            itemKey: 'entity_id'
        }
    });
});