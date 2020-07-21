define([
    'uiComponent',
    'Magento_Checkout/js/model/totals',
    'mage/translate',
], function (Component, totals, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_Rewards/highlight',
            captionEndText: $t('for completing your purchase!'),
            captionStartText : $t('You can earn'),
            frontend_class: '',
            highlight: []
        },
        initObservable: function () {
            this._super().observe(['highlight']);
            totals.totals.subscribe(this.getHighlightData.bind(this));
            return this;
        },

        getHighlightData: function (totals) {
            if (totals.extension_attributes && totals.extension_attributes.amasty_rewards_highlight) {
                this.highlight(totals.extension_attributes.amasty_rewards_highlight);
            } else {
                this.highlight({'visible':false});
            }
        },
    });
});
