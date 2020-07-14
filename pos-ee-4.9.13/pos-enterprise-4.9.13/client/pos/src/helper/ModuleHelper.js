import Config from '../config/Config';
import permission from './Permission';
import PermissionConstant from "../view/constant/PermissionConstant";

export default {
    modules: null,

    /**
     * get all module
     * @returns {null}
     */
    getModules() {
        if (!Config.config || !Config.config.enable_modules) {
            return null;
        }
        this.modules = Config.config.enable_modules;
        return this.modules;
    },

    /**
     * enable module inventory
     * @returns {boolean}
     */
    enableModuleInventory() {
        if (!this.getModules())
            return false;
        return this.getModules().find(module => module === 'Magestore_InventorySuccess');
    },

    /**
     * enable module inventory MSI
     * @returns {boolean}
     */
    enableModuleMSI(){
        if(!this.getModules()){
            return false;
        }
        return this.getModules().find(module => module === 'Magento_InventoryMSI');
    },

    /**
     *  check is webpos standard
     * @returns {boolean}
     */
    isWebposStandard(){
        if (!Config.config || !Config.config.is_webpos_standard) {
            return false;
        }
        return Config.config.is_webpos_standard;
    },

    /**
     * allow show external stock MSI
     * @returns {boolean}
     */
    isAllowShowExternalStockMSI (){
        if(!this.isWebposStandard() && this.enableModuleMSI()){
            return true;
        }
        return false;
    },

    /**
     * allow check external stock
     * @returns {boolean}
     */
    isAllowCheckExternalStock(){
        let externalStockPermission =  permission.isAllowed(PermissionConstant.PERMISSION_CHECK_EXTERNAL_STOCK);
        if(externalStockPermission && (this.enableModuleInventory() || this.isAllowShowExternalStockMSI())){
            return true;
        }
        return false;
    }

}