/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'Magento_Ui/js/form/element/text',
    'uiRegistry'
], function (_, Text, registry) {
    'use strict';

    return Text.extend({
        visibleStage: function (data) {
            let status = registry.get(this.provider).data.transfer_summary.general_information.status;
            if(data === 'new' && status === 'open') {
                this.visible(false);
            } else {
                this.visible(true);
            }
        }
    });
});
