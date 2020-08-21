/**
 * Copyright   2018 Ayasoftware (http://www.ayasoftware.com).
 * See COPYING.txt for license details.
 * author      EL HASSAN MATAR <support@ayasoftware.com>
 */
var config = {
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Ayasoftware_EnhancedConfigurable/js/swatches': true
            }, 
            'Magento_ConfigurableProduct/js/configurable': {
                'Ayasoftware_EnhancedConfigurable/js/configurable': true
            }
        }
    }  
};