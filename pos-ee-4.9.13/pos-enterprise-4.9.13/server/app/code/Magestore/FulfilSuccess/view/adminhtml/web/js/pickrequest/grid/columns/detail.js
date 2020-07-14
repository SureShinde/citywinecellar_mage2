/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magestore_FulfilSuccess/js/grid/columns/detail',
    'jquery',
], function (Column, $) {
    'use strict';

    return Column.extend({
        defaults: {
            modalId: 'pick_request_detail_holder',
            itemKey: 'pick_request_id'
        }
    });
});