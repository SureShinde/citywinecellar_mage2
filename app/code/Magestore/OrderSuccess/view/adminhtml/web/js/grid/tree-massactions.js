/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'Magento_Ui/js/grid/tree-massactions'
], function (ko, Massactions) {
    'use strict';

    return Massactions.extend({
        defaults: {
            template: 'Magestore_OrderSuccess/grid/tree-massactions',
            submenuTemplate: 'Magestore_OrderSuccess/grid/submenu',
            listens: {
                opened: 'hideSubmenus'
            }
        }
    });
});
