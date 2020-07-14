import CoreService from "../CoreService";
import ServiceFactory from "../../framework/factory/ServiceFactory";
import CategoryResourceModel from "../../resource-model/catalog/CategoryResourceModel";
import QueryService from "../QueryService";
import SyncConstant from "../../view/constant/SyncConstant";
import Config from "../../config/Config";

export class CategoryService extends CoreService {
    static className = 'CategoryService';
    resourceModel = CategoryResourceModel;

    /**
     * get list category with parent category
     * @param parentId
     * @returns {Promise<void>}
     */
    async getListCategory(parentId) {
        let result = {};
        let queryService = QueryService.reset();
        if (Config.dataTypeMode[SyncConstant.TYPE_CATEGORY] === SyncConstant.OFFLINE_MODE) {
            queryService.addFieldToFilter('id', parentId, 'eq');
        } else {
            queryService.addFieldToFilter('entity_id', parentId, 'eq');
        }
        let response = await this.getResourceModel().getList(queryService);
        result.parentCategory = response.items[0];

        queryService = QueryService.reset();
        queryService.setOrder('position');
        queryService.addFieldToFilter('parent_id', parentId, 'eq');
        response = await this.getResourceModel().getList(queryService);
        result.items = response.items;
        result.search_criteria = response.search_criteria;
        result.total_count = response.total_count;
        return result;
    }

}

/** @type CategoryService */
let categoryService = ServiceFactory.get(CategoryService);

export default categoryService;
