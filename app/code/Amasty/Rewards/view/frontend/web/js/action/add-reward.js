define([
        'ko',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Amasty_Rewards/js/model/resource-url-manager',
        'Magento_Checkout/js/model/error-processor',
        'Amasty_Rewards/js/model/payment/reward-messages',
        'mage/storage',
        'mage/translate',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/full-screen-loader'
    ], function (ko, $, quote, urlManager, errorProcessor, messageContainer,
        storage, $t, getPaymentInformationAction, totals, fullScreenLoader
    ) {
        'use strict';
        return function (points, isApplied, pointsLeftObs, rateForCurrency, noticeMessage) {
            var quoteId = quote.getQuoteId(),
                url = urlManager.getRewardsUrl(points(), quoteId);

            messageContainer.clear();
            fullScreenLoader.startLoader();

            return storage.put(
                url,
                {},
                false
            ).done(function (response) {
                var deferred,
                    pointsUsed = 0;

                if (response) {
                    pointsUsed = response[0];
                    noticeMessage($t(response[1]));
                    $('[data-amrewards-js="notice-message"]').show();
                    setTimeout(function () {
                        $('[data-amrewards-js="notice-message"]').hide('blind', {}, 500);
                    }, 5000);

                    deferred = $.Deferred();

                    isApplied(true);
                    totals.isLoading(true);
                    getPaymentInformationAction(deferred);

                    $.when(deferred).done(function () {
                        points((pointsUsed).toFixed(2));
                        pointsLeftObs((pointsLeftObs() - points()).toFixed(2));
                        $('#amreward_amount').val(points()).change();

                        fullScreenLoader.stopLoader();
                        totals.isLoading(false);
                    });
                }
            }).fail(function (response) {
                fullScreenLoader.stopLoader();
                totals.isLoading(false);
                errorProcessor.process(response, messageContainer);
            });
        };
    }
);