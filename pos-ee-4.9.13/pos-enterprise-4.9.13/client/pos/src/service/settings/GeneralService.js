import ServiceFactory from "../../framework/factory/ServiceFactory";
import LocalStorageHelper from "../../helper/LocalStorageHelper";
import GeneralConstant from "../../view/constant/settings/GeneralConstant";
import toNumber from "lodash/toNumber";
import ConfigHelper from "../../helper/ConfigHelper";
import DeviceHelper from "../../helper/DeviceHelper";

export class GeneralService {
    static className="GeneralService";

    /**
     * check is use offline data
     * @returns {boolean}
     */
    isUseOfflineData() {
        if(LocalStorageHelper.get(GeneralConstant.GET_IS_SYNC_DATA_TO_BROWSER_STORAGE) === null) {
            this.resetPosMode();
        }

        return !!toNumber(LocalStorageHelper.get(GeneralConstant.GET_IS_SYNC_DATA_TO_BROWSER_STORAGE));
    }

    /**
     * @param value
     * @returns {*}
     */
    setUseOfflineData(value) {
        return LocalStorageHelper.set(GeneralConstant.GET_IS_SYNC_DATA_TO_BROWSER_STORAGE, value ? 1 : 0);
    }

    /**
     * Reset pos mode
     */
    resetPosMode() {
        let mode = ConfigHelper.getConfig(GeneralConstant.CONFIG_XML_PATH_PERFORMANCE_POS_DEFAULT_MODE);
        if (DeviceHelper.isMobile()) {
            mode = ConfigHelper.getConfig(GeneralConstant.CONFIG_XML_PATH_PERFORMANCE_POS_TABLET_DEFAULT_MODE);
        }
        mode = !!(mode && mode === '1');
        this.setUseOfflineData(mode);
    }
}

/**
 * @type {GeneralService}
 */
let settingsGeneralService = ServiceFactory.get(GeneralService);

export default settingsGeneralService;
