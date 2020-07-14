import {isMobile, isIOS, isAndroid} from 'react-device-detect';

export default {
    /**
     * Check is using mobile device
     * @returns {boolean}
     */
    isMobile() {
        return isMobile || this.isIOS();
    },

    /**
     * Check is using ios device
     * @returns {boolean}
     */
    isIOS() {
        return isIOS || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    },

    /**
     * Check is using android device
     * @returns {boolean}
     */
    isAndroid() {
        return isAndroid;
    }
}
