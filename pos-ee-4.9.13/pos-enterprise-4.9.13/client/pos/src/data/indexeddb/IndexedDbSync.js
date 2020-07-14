import Abstract from './IndexedDbAbstract';
import SyncConstant from '../../view/constant/SyncConstant';
import {fire} from "../../event-bus";

export default class IndexedDbSync extends Abstract {
    static className = 'IndexedDbSync';
    main_table = 'sync';
    primary_key = 'type';
    offline_id_prefix = '';

    initialSyncData = [
        {
            type: SyncConstant.TYPE_CATALOG_RULE_PRODUCT_PRICE,
            count: 0,
            total: SyncConstant.DEFAULT_TOTAL,
            updated_time: null,
            updated_data_time: null,
            sort_order: 10
        },
        {
            type: SyncConstant.TYPE_STOCK,
            count: 0,
            total: SyncConstant.DEFAULT_TOTAL,
            updated_time: null,
            updated_data_time: null,
            sort_order: 20
        },
        {
            type: SyncConstant.TYPE_PRODUCT,
            count: 0,
            total: SyncConstant.DEFAULT_TOTAL,
            updated_time: null,
            updated_data_time: null,
            sort_order: 30
        },
        {
            type: SyncConstant.TYPE_CUSTOMER,
            count: 0,
            total: SyncConstant.DEFAULT_TOTAL,
            updated_time: null,
            updated_data_time: null,
            sort_order: 40
        },
        {
            type: SyncConstant.TYPE_ORDER,
            count: 0,
            total: SyncConstant.DEFAULT_TOTAL,
            updated_time: null,
            updated_data_time: null,
            sort_order: 50
        },
        {
            type: SyncConstant.TYPE_SESSION,
            count: 0,
            total: SyncConstant.DEFAULT_TOTAL,
            updated_time: null,
            updated_data_time: null,
            sort_order: 60
        },
        {
            type: SyncConstant.TYPE_CATEGORY,
            count: 0,
            total: SyncConstant.DEFAULT_TOTAL,
            updated_time: null,
            updated_data_time: null,
            sort_order: 70
        }
    ];

    /**
     * constructor
     *
     * @param props
     */
    constructor(props) {
        super(props);
        let eventDataBefore = {
            initialSyncData: this.initialSyncData
        };

        fire('indexed_db_sync_constructor_before', eventDataBefore);
        this.initialSyncData = eventDataBefore.initialSyncData;
    }

    /**
     * Set Default data of Sync table when Sync table is empty
     */
    async setDefaultData() {
        let result = await this.getAll();
        if (!result.length) {
            await this.bulkPut(this.initialSyncData);
            return this.initialSyncData;
        }
        return null;
    }

    /**
     * Get default sync data
     */
    getDefaultData() {
        return this.initialSyncData;
    }

    /**
     * Get default data type mode
     */
    getDefaultDataTypeMode() {
        let dataTypeMode = {};
        this.initialSyncData.forEach(data => dataTypeMode[data.type] = SyncConstant.ONLINE_MODE);
        return dataTypeMode;
    }

    /**
     * Reset items's data of sync table in indexedDb
     * @param items
     * @returns {Promise<any>}
     */
    resetData(items) {
        let itemList = this.initialSyncData.filter(function (data) {
            return items.indexOf(data.type) >= 0;
        });
        return this.bulkPut(itemList);
    }
}
