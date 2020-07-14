import CategoryConstant from '../constant/CategoryConstant';

export default {
    /**
     * action get list category
     * @param parent_id
     * @returns {{parent_id: *, type: string}}
     */
    getListCategory: (parent_id = null) => {
        return {
            type: CategoryConstant.GET_LIST_CATEGORY,
            parent_id: parent_id
        }
    },

    /**
     * action result list category
     * @param parentCategory
     * @param categories
     * @param search_criteria
     * @param total_count
     * @param parent_id
     * @param requestMode
     * @returns {{total_count: number, parent_id: *, parentCategory: *, categories: Array, requestMode: *, type: string, search_criteria}}
     */
    getListCategoryResult: (parentCategory = null, categories = [], search_criteria = {}, total_count = 0, parent_id = null, requestMode) => {
        return {
            type: CategoryConstant.GET_LIST_CATEGORY_RESULT,
            parentCategory: parentCategory,
            categories: categories,
            search_criteria: search_criteria,
            total_count: total_count,
            parent_id: parent_id,
            requestMode: requestMode
        }
    }
}
