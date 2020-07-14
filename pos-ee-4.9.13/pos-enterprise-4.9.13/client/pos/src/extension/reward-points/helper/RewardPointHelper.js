import * as _ from "lodash";
import ConfigHelper from "../../../helper/ConfigHelper";
import Config from '../../../config/Config'

export class RewardPointHelper {
    /**
     *
     * @return {number}
     */
    static isEnabledRewardPoint() {
        const isEnabled = ConfigHelper.getConfig('rewardpoints/general/enable');
        return isEnabled * 1;
    }
    /**
     * @return {*|string}
     */
    static getPointName() {
        return ConfigHelper.getConfig("rewardpoints/general/point_name") || "Point";
    }
    /**
     * @return {*|string}
     */
    static getPluralOfPointName() {
        return ConfigHelper.getConfig("rewardpoints/general/point_names") || "Points";
    }

    /**
     *
     * @return {*|string}
     */
    static getEarningRoundMethod() {
        return ConfigHelper.getConfig("rewardpoints/earning/rounding_method") || "round";
    }

    /**
     *
     * @return {*|number}
     */
    static getEarningMaxBalance() {
        return _.toNumber(ConfigHelper.getConfig("rewardpoints/earning/max_balance")) || 0;
    }

    /**
     *
     * @return {*|number}
     */
    static canEarnByTax() {
        return ConfigHelper.getConfig("rewardpoints/earning/by_tax") * 1 || 0;
    }

    /**
     *
     * @return {*|number}
     */
    static canEarnByShipping() {
        return ConfigHelper.getConfig("rewardpoints/earning/by_shipping") * 1 || 0;
    }

    /**
     *
     * @return {*|number}
     */
    static canEarnWhenSpend() {
        return ConfigHelper.getConfig("rewardpoints/earning/earn_when_spend") * 1 || 0;
    }
    /**
     *
     * @return {*|number}
     */
    static allowReceivingPointsWhenInvoiceIsCreated() {
        return ConfigHelper.getConfig("rewardpoints/earning/order_invoice") * 1 || 0;
    }
    /**
     *
     * @return {*|number}
     */
    static holdPointDay() {
        return _.toNumber(ConfigHelper.getConfig("rewardpoints/earning/holding_days")) || 0;
    }
    /**
     *
     * @return {*|number}
     */
    static getMinimumRedeemablePoint() {
        return _.toNumber(ConfigHelper.getConfig("rewardpoints/spending/redeemable_points")) || 0;
    }
    /**
     *
     * @return {*|number}
     */
    static getSpendMaxPointPerOrder() {
        return _.toNumber(ConfigHelper.getConfig("rewardpoints/spending/max_points_per_order")) || 0;
    }
    /**
     *
     * @return {*|number}
     */
    static isSpendMaxPointAsDefault() {
        return ConfigHelper.getConfig("rewardpoints/spending/max_point_default") * 1 || 0;
    }

    /**
     *
     * @return {*|number}
     */
    static allowSpendForShippingFee() {
        return ConfigHelper.getConfig("rewardpoints/spending/spend_for_shipping") * 1 || 0;
    }

    /**
     *
     * @return {array}
     */
    static getRates() {
        return Config.config.extension_attributes.rewardpoints_rate
            ? Config.config.extension_attributes.rewardpoints_rate.filter(rate => ((rate.status * 1) === 1))
            : [];
    }

    /**
     *
     * @return {array}
     */
    static getEarningRates() {
        return this.getRates().filter(rate => ((rate.direction * 1) === 2));
    }

    /**
     *
     * @return {array}
     */
    static getSpendingRates() {
        return this.getRates().filter(rate => ((rate.direction * 1) === 1));
    }

    /**
     *
     * @param number
     * @return {number}
     */
    static round(number) {
        return Math[this.getEarningRoundMethod()](number);
    }

}
