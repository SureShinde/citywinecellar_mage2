define([
    'uiComponent',
    'mage/translate',
], function (Component, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_Rewards/highlight',
            captionEndText: $t('for registration!'),
            captionStartText : $t('You can earn'),
            frontend_class: '',
            highlight: []
        },

        initObservable: function () {
            this._super().observe(['highlight']);

            return this;
        },
    });
});
