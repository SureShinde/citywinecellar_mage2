import deepmerge from "../framework/Merge";
// IMPORT_LINES
import __0_payment_offlineConfig from './payment-offline/etc/config.js';
import __1_reward_pointsConfig from './reward-points/etc/config.js';
import __2_stripe_terminal_paymentConfig from './stripe-terminal-payment/etc/config.js';
import __3_take_shipmentConfig from './take-shipment/etc/config.js';
// IMPORT_LINES

/**
 * Collect all config.js each extension module
 *
 * @return {*}
 */
function getConfig() {
    return deepmerge.all([
        {},
        {},
        // MODULE_LINES
        __0_payment_offlineConfig,
        __1_reward_pointsConfig,
        __2_stripe_terminal_paymentConfig,
        __3_take_shipmentConfig,
        // MODULE_LINES
    ])
}

let cachedConfig = getConfig();

/**
 *
 * cache config
 *
 * @return {*}
 */
export default () => {
    if (!cachedConfig) {
        cachedConfig = getConfig()
    }

    return cachedConfig
}

export function updateConfig(newConfig) {
    cachedConfig = newConfig;
}
