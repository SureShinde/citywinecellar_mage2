var config = {
    map: {
        '*': {
            'Magento_Checkout/js/model/checkout-data-resolver': 'Laconica_Checkout/js/model/checkout-data-resolver'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Laconica_Checkout/js/mixin/shipping-mixin': true
            }
        }
    }
};