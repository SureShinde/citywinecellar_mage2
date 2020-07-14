/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'mage/translate',
        'Magestore_Webpos/js/model/pos/session/management'
    ],
    function ($, ko, Component, __, PosManagement) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/pos/session/no-session'
            },
            error: ko.pureComputed(function () {
                return PosManagement.error();
            }),
            refreshData: function(){
                PosManagement.refreshData();
            }
        });
    }
);
