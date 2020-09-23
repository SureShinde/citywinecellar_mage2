define(
    [
        'jquery',
        'underscore',
        'Magento_Ui/js/form/form',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry',
        'mage/translate',
        'Magento_Checkout/js/model/shipping-rate-service'
    ], function (
        $,
        _,
        Component,
        ko,
        customer,
        addressList,
        addressConverter,
        quote,
        createShippingAddress,
        selectShippingAddress,
        shippingRatesValidator,
        formPopUpState,
        shippingService,
        selectShippingMethodAction,
        rateRegistry,
        setShippingInformationAction,
        stepNavigator,
        modal,
        checkoutDataResolver,
        checkoutData,
        registry,
        $t) {
        'use strict';

        var mixin = {

            validateShippingInformation: function () {

                let regions = [];
                let selectedRegionId = parseInt(quote.shippingAddress().regionId);

                if (!selectedRegionId || typeof window.valuesConfig === "undefined" || !window.valuesConfig.enabled) {
                    return this._super();
                }

                let messageContainer = registry.get('checkout.errors').messageContainer;

                // Collect valid regions
                jQuery("select[name='region_id'] option").each(function (index, el) {
                    let regionId = parseInt(jQuery(el).val());
                    if (regionId && !regions.includes(regionId)) {
                        regions.push(regionId);
                    }
                });

                // Check if selected region is valid
                if (!regions.includes(selectedRegionId)) {
                    let messageText = window.valuesConfig.error_message;
                    this.errorValidationMessage(
                        $t(messageText)
                    );
                    messageContainer.addErrorMessage({
                        message: $t(messageText)
                    });
                    return false;
                }

                // Additional zip validation
                let enteredZipCode = quote.shippingAddress().postcode;
                if (!enteredZipCode || typeof window.valuesConfig === "undefined" || !window.valuesConfig.zip_enabled) {
                    return this._super();
                }

                let responseResult = [];
                $.ajax({
                    type: "POST",
                    url: BASE_URL + "la_checkout/connection/zip",
                    dataType: 'json',
                    async: false,
                    data: {zip: enteredZipCode, region: selectedRegionId},
                    success: function (data) {
                        responseResult = data;
                    }
                });

                if (!responseResult['region_common'] && !responseResult['status']) {
                    let messageText = window.valuesConfig.zip_error_message;
                    this.errorValidationMessage(
                        $t(messageText)
                    );
                    messageContainer.addErrorMessage({
                        message: $t(messageText)
                    });
                    return false;
                }

                return this._super();
            }
        };

        return function (target) {
            return target.extend(mixin);
        };
    }
);